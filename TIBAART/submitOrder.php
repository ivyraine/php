<?php

include('cross_domain.php');
include ('conn.php');

// 1. 檢查請求方法: POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '無效的請求方法。只接受 POST 請求。'
    ]);
    exit(); // 終止腳本執行
}


// 2. encode前端送的 JSON
$json_data = file_get_contents('php://input'); // 讀取原始的請求主體
$data = json_decode($json_data, true); // JSON 字串解碼

// 檢查 JSON 是否成功解析
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => '無效的 JSON 資料格式。',
        'json_error' => json_last_error_msg() // 方便除錯
    ]);
    exit();
}

// 3. 提取並驗證接收到的資料
// 從 $data 陣列中提取訂單資訊
$orderInfo = $data['orderInfo'] ?? null;
$carrier = $data['carrier'] ?? null;
$recipientInfo = $data['recipientInfo'] ?? null;
$cartItems = $data['cartItems'] ?? [];
$totalPrice = $data['totalPrice'] ?? 0;
$shipping_fee = $data['shippingFee'] ?? 0;
$discount = $data['discount'] ?? 0;
$member_id = $orderInfo['member_id'] ?? null; 

// 資料驗證
if (empty($orderInfo) || empty($recipientInfo) || !is_array($cartItems) || count($cartItems) === 0 || $totalPrice <= 0) {
    echo json_encode([
        'success' => false,
        'message' => '必要訂單資料不完整或無效。'
    ]);
    exit();
}

// 4. 處理訂單
$order_success = false;
$response_message = '';
$order_id = null; // 初始化為 null

try {
    // 資料庫連線
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->beginTransaction();
    
    // 生成訂單編號 (時間戳 + 隨機數)
    $order_number = date('ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // 計算總金額
    $subtotal_amount = 0;
    foreach ($cartItems as $item) {
        $qty = $item['quantity'] ?? 0;
        $price = $item['price'] ?? 0;
        $subtotal_amount += $qty * $price;
    }
    
    $total_amount = $subtotal_amount + $shipping_fee;
    
    
    // insert 訂單資料到 ORDERS 
    $stmt_order = $pdo->prepare("
        INSERT INTO ORDERS (
            order_number, 
            member_id, 
            order_date, 
            address, 
            contact_name, 
            contact_phone, 
            carrier,
            shipping_fee, 
            total_amount, 
            status, 
            payment_status,
            payment_method
        ) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // 準備訂單資料
    $member_id = $orderInfo['member_id'] ?? null;
    $address = $recipientInfo['address'] ?? '';
    $contact_name = $recipientInfo['name'] ?? '';
    $contact_phone = $recipientInfo['tel'] ?? '';
    $carrier = $orderInfo['carrier'] ?? '';

    $status = 'pending';
    $payment_status = 'unpaid';
    $payment_method = 'Credit Card';
    
    $stmt_order->execute([
        $order_number,
        $member_id,
        $address,
        $contact_name,
        $contact_phone,
        $carrier,
        $shipping_fee,
        $total_amount,
        $status,
        $payment_status,
        $payment_method
    ]);
    
    $lastInsertOrderId = $pdo->lastInsertId();
    
    // insert 訂單商品明細到 ORDER_DETAIL 
    $stmt_item = $pdo->prepare("
        INSERT INTO ORDER_DETAIL (
            order_id, 
            product_id, 
            product_name, 
            unit_price, 
            quantity, 
            subtotal
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($cartItems as $item) {
        $product_id = $item['id'] ?? null;
        $product_name = $item['name'] ?? '';
        $unit_price = $item['price'] ?? 0;
        $quantity = $item['quantity'] ?? 0;
        $subtotal = $quantity * $unit_price;
        
        if ($product_id && $quantity > 0) {
            $stmt_item->execute([
                $lastInsertOrderId,
                $product_id,
                $product_name,
                $unit_price,
                $quantity,
                $subtotal
            ]);
        }
    }
    
    // 提交
    $pdo->commit();
    
    $order_success = true;
    $order_id = $order_number; 
    $response_message = '訂單建立成功，訂單編號：' . $order_number;
    
} catch (PDOException $e) {
    // 回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $order_success = false;
    $response_message = '訂單建立失敗，請稍後再試';
    error_log("Database error: " . $e->getMessage());
    
    // debug (正式環境建議隱藏)
    if (defined('DEBUG') && DEBUG === true) {
        $response_message .= '：' . $e->getMessage();
    }
}

// 5. 回傳 JSON 給前端
if ($order_success) {
    echo json_encode([
        'success' => true,
        'message' => '訂單已成功送出！',
        'orderId' => $order_id // 回傳訂單 ID
    ]);
} else {
    // 如果處理失敗...
    echo json_encode([
        'success' => false,
        'message' => $response_message ?: '訂單處理失敗，請檢查資料或稍後再試。'
    ]);
}

exit();