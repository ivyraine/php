<?php
// 設置 CORS 標頭，允許來自前端的跨域請求
// 請注意：在生產環境中，建議將 '*' 替換為你的前端網域，例如 'http://localhost:8080'
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // 允許的 HTTP 方法
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json'); // 告訴客戶端回傳的是 JSON 格式

$db_host = "127.0.0.1";
// $db_host = "localhost";
$db_user = "root";
$db_pass = "password";
$db_select = "TIBAART";

$dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8mb4";

// 處理 OPTIONS 預檢請求 (Preflight request)，通常在跨域 POST 請求前會發送
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---------------------------
// 1. 檢查請求方法是否為 POST
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '無效的請求方法。只接受 POST 請求。'
    ]);
    exit(); // 終止腳本執行
}

// ---------------------------
// 2. 獲取並解析前端發送的 JSON 資料
// ---------------------------
$json_data = file_get_contents('php://input'); // 讀取原始的請求主體
$data = json_decode($json_data, true); // 將 JSON 字串解碼為 PHP 關聯陣列

// 檢查 JSON 是否成功解析
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => '無效的 JSON 資料格式。',
        'json_error' => json_last_error_msg() // 方便除錯
    ]);
    exit();
}

// ---------------------------
// 3. 提取並驗證接收到的資料
// ---------------------------
// 從 $data 陣列中提取訂單資訊
// 使用 ?? 運算符提供預設值，避免 undefined index 錯誤
$orderInfo = $data['orderInfo'] ?? null;
$recipientInfo = $data['recipientInfo'] ?? null;
$cartItems = $data['cartItems'] ?? [];
$totalPrice = $data['totalPrice'] ?? 0;
$shippingFee = $data['shippingFee'] ?? 0;
$discount = $data['discount'] ?? 0;

// 簡單的資料驗證範例：檢查必要欄位是否存在
if (empty($orderInfo) || empty($recipientInfo) || !is_array($cartItems) || count($cartItems) === 0 || $totalPrice <= 0) {
    echo json_encode([
        'success' => false,
        'message' => '必要訂單資料不完整或無效。'
    ]);
    exit();
}

// 你可以進一步驗證每個子欄位的資料格式和內容
// 例如：
// if (!isset($recipientInfo['name']) || empty($recipientInfo['name'])) { ... }

// ---------------------------
// 4. 處理訂單邏輯 (示範部分)
// ---------------------------
// 在這裡，你會執行實際的訂單處理邏輯，例如：
// - 將訂單資料寫入資料庫 (例如 orders 表、order_items 表)
// - 扣除庫存
// - 處理支付（如果這部分也在這裡）
// - 發送確認郵件
// - 生成唯一的訂單號等

$order_success = true; // 假設訂單處理成功，實際應根據資料庫操作結果來判斷
$order_id = 'ORD' . uniqid(); // 生成一個簡單的模擬訂單 ID

// 實際的資料庫操作範例（請替換為你的資料庫連線和操作）
/*
try {
    // 假設你已經設定了資料庫連線 $pdo
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, total_price, status, created_at, ...) VALUES (?, ?, ?, NOW(), ...)");
    $stmt->execute([$order_id, $totalPrice, 'pending', ...]);
    $lastInsertOrderId = $pdo->lastInsertId(); // 如果 orders 表有自增 ID

    // 插入訂單商品明細
    foreach ($cartItems as $item) {
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, ...) VALUES (?, ?, ?, ?, ...)");
        $stmt_item->execute([$lastInsertOrderId, $item['id'], $item['quantity'], $item['price']]);
    }

    // 如果所有操作都成功
    $order_success = true;

} catch (PDOException $e) {
    $order_success = false;
    error_log("Database error: " . $e->getMessage()); // 記錄到伺服器錯誤日誌
    $response_message = '資料庫操作失敗，請稍後再試。';
}
*/

// ---------------------------
// 5. 回傳 JSON 回應給前端
// ---------------------------
if ($order_success) {
    echo json_encode([
        'success' => true,
        'message' => '訂單已成功送出！',
        'orderId' => $order_id // 回傳訂單 ID
    ]);
} else {
    // 如果處理失敗（例如資料庫錯誤或資料驗證失敗）
    echo json_encode([
        'success' => false,
        'message' => $response_message ?? '訂單處理失敗，請檢查資料或稍後再試。'
    ]);
}

exit(); // 確保腳本在回傳 JSON 後立即終止
?>