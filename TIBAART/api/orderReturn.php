<?php

include('cross_domain.php');
include ('conn.php');

// 接收 綠界 回傳的資料
$input = file_get_contents('php://input');

// 記錄回傳的資料，方便除錯
file_put_contents('ecpay_log.txt', date('Y-m-d H:i:s') . ' - ' . $input . PHP_EOL, FILE_APPEND);

// 確認付款成功
if (isset($_POST['RtnCode']) && $_POST['RtnCode'] == '1') {

    // 從回傳的資料中找到 order_number
    $order_number = $_POST['MerchantTradeNo'];

    // 更新狀態、更新時間
    $stmt = $mysqli->prepare("
        UPDATE ORDERS
        SET payment_status = 'paid', updated_at = NOW()
        WHERE order_number = ?
    ");
    $stmt->bind_param('s', $order_number);
    $stmt->execute();
}

// 回傳給綠界，通知「接收成功」
header('Content-Type: text/plain; charset=utf-8');
echo "1|OK";
exit;

?>