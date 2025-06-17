<?php
// 接收 綠界 回傳的資料
$input = file_get_contents('php://input');

// 記錄回傳的資料，方便除錯
file_put_contents('ecpay_log.txt', date('Y-m-d H:i:s') . ' - ' . $input . PHP_EOL, FILE_APPEND);

// 資料庫設定
// $db_host = "127.0.0.1";
// $db_user = "root";
// $db_pass = "password";
// $db_select = "TIBAART";

$db_host = "127.0.0.1";
$db_user = "tibamefe_since2021";
$db_pass = "vwRBSb.j&K#E";
$db_select = "tibamefe_tjd101g2";

// 連接資料庫
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_select);

$mysqli->set_charset("utf8");


// 確認付款成功
if (isset($_POST['RtnCode']) && $_POST['RtnCode'] == '1') {

    // 從回傳的資料中找到 donate_no
    $donate_no = $_POST['MerchantTradeNo'];

    // 更新狀態、更新時間
    $stmt = $mysqli->prepare("
        UPDATE DONATE
        SET status = 'paid', updated_at = NOW()
        WHERE donate_no = ?
    ");
    $stmt->bind_param('s', $donate_no);
    $stmt->execute();
}

// 回傳給綠界，通知「接收成功」
header('Content-Type: text/plain; charset=utf-8');
echo "1|OK";
exit;

?>