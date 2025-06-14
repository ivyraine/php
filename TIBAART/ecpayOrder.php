<?php

include('cross_domain.php');
include ('conn.php');

date_default_timezone_set('Asia/Taipei');

// 綠界（測試環境）
$hashKey = 'pwFHCqoQZGmho4w6';
$hashIV = 'EkRm7iFT261dpevs';
$merchantID = '3002607';  // '2000132';

// 檢查請求方法: POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '無效的請求方法。只接受 POST 請求。'
    ]);
    exit();
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// 從 $data 陣列中提取訂單資訊
$order_number = $data['order_number'];

// 連資料庫
try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT order_number, total_amount, contact_name, contact_phone, payment_method FROM ORDERS WHERE order_number = ?");
    $stmt->execute([$order_number]);
    $order_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order_data) {
        echo json_encode(['success' => false, 'message' => '訂單不存在']);
        exit();
    }

    $total_amount = (int)$order_data['total_amount'];
    $contact_name = $order_data['contact_name'];
    $contact_phone = $order_data['contact_phone'];

    // 查ORDER_DETAIL 取商品明細
    $stmt_items = $pdo->prepare("SELECT product_name, quantity FROM ORDER_DETAIL WHERE order_id = (SELECT id FROM ORDERS WHERE order_number = ?)");
    $stmt_items->execute([$order_number]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $item_names = [];
    foreach ($items as $item) {
        $item_names[] = $item['product_name'] . ' x' . $item['quantity'];
    }
    $item_name_string = implode('#', $item_names) ?: '商品名稱';

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫錯誤：' . $e->getMessage()]);
    exit();
}

// ==================================== 綠界 ====================================

// 組訂單資訊
$order = [
    'MerchantID' => $merchantID,
    'MerchantTradeNo' => $order_number, // 我們的訂單編號
    'MerchantTradeDate' => date('Y/m/d H:i:s'),
    'PaymentType' => 'aio',
    'TotalAmount' =>  (int)$total_amount,
    'TradeDesc' => '緯藝訂單',
    'ItemName' => $item_name_string,
     // 'ReturnURL' => 'https://tibamef2e.com/tjd101/g2/ecpayOrder.php',
    'ReturnURL' => 'http://localhost/TIBAART/orderReturn.php', // 後端回調 URL
    // 'ClientBackURL' => 'https://tibamef2e.com/tjd101/g2/front/OrderConfirm', // 前端返回 URL
    'ClientBackURL' => 'http://localhost:5173/tjd101/g2/front/OrderConfirm', // 前端返回 URL
    'ChoosePayment' => 'Credit',
    'EncryptType' => 1,
    'Email' => $contact_phone.'@example.com'
];

// 加密用的函式
function generateCheckMacValue($params, $hashKey, $hashIV) {
    ksort($params);
    $encoded = http_build_query($params);
    $encoded = urldecode($encoded);
    $macString = "HashKey=$hashKey&$encoded&HashIV=$hashIV";
    $macString = urlencode($macString);
    $macString = strtolower($macString);
    $macString = str_replace('%21', '!', $macString);
    $macString = str_replace('%28', '(', $macString);
    $macString = str_replace('%29', ')', $macString);
    $macString = str_replace('%2a', '*', $macString);
    $macString = str_replace('%2d', '-', $macString);
    $macString = str_replace('%2e', '.', $macString);
    $macString = str_replace('%5f', '_', $macString);
    $macString = str_replace('%7e', '~', $macString);
    return strtoupper(hash('sha256', $macString));
}

$order['CheckMacValue'] = generateCheckMacValue($order, $hashKey, $hashIV);

// 回傳整個 HTML 表單給 Vue3 前端，讓網頁跳轉到綠界
echo '<form id="ecpay-form" method="post" action="https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5">';
foreach ($order as $key => $value) {
    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
}
echo '</form>';
echo '<script>document.getElementById("ecpay-form").submit();</script>';

?>
