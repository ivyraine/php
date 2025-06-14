<?php

// 跨網域接收
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

// 處理 OPTIONS 預檢請求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// 1. 資料庫設定
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "password";
$db_select = "TIBAART";

// $db_host = "127.0.0.1";
// $db_user = "tibamefe_since2021";
// $db_pass = "vwRBSb.j&K#E";
// $db_select = "tibamefe_tjd101g2";

date_default_timezone_set('Asia/Taipei');

// 綠界設定（測試環境）
$hashKey = 'pwFHCqoQZGmho4w6';
$hashIV = 'EkRm7iFT261dpevs';
$merchantID = '3002607';  // '2000132';

// 接收並驗證前端資料
// $expoN = $_POST['expoN'] ?? '';  // 前端傳來的字串 ID (固定資料測試用)
$d_name = $_POST['d_name'] ?? '贊助者';
$email = $_POST['email'] ?? 'no@email.com';
$amount = $_POST['amount'] ?? 0;

// =====================================================================

// 4. 展覽資料對應（與前端保持一致）
$expo_id = isset($_POST['expoN']) ? intval($_POST['expoN']) : 0;
if ($expo_id <= 0) {  // 如果 expo_id 是 0 或負數（前端沒送資料或是送錯）
    echo json_encode(['error' => '無效的展覽 ID']);
    exit;
}

try {
    // 5. 連接資料庫
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_select);
    
    if ($mysqli->connect_error) {
        throw new Exception("資料庫連線失敗: " . $mysqli->connect_error);
    }

    $mysqli->set_charset("utf8");

    // 6. 生成唯一訂單編號
    $donate_no = 'DNT' . date('YmdHi') . rand(100, 999);

    // 7. 先檢查資料表是否存在，不存在則建立
    $check_table = "SHOW TABLES LIKE 'DONATE'";
    $result = $mysqli->query($check_table);

    if ($result->num_rows == 0) {
        // 建立簡化版 DONATE 資料表
        $create_table = "
            CREATE TABLE `DONATE` (
                `id` int NOT NULL AUTO_INCREMENT,
                `donate_no` varchar(20) NOT NULL COMMENT '贊助編號',
                `d_name` varchar(45) NOT NULL COMMENT '贊助人姓名',
                `d_email` varchar(45) NOT NULL COMMENT '贊助人信箱',
                `amount` int NOT NULL COMMENT '贊助金額',
                `share` int NOT NULL DEFAULT '10' COMMENT '抽成',
                `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT '交易狀態',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
                `member_id` int NULL,
                `artist_id` int NOT NULL,
                `expo_id` int NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `donate_no_UNIQUE` (`donate_no`),
                KEY `FK_DONATE_MEMBER_idx` (`member_id`),
                KEY `FK_DONATE_ARTIST_idx` (`artist_id`),
                KEY `FK_DONATE_EXPO_idx` (`expo_id`),
                CONSTRAINT `FK_DONATE_ARTIST` FOREIGN KEY (`artist_id`) REFERENCES `ARTIST` (`id`),
                CONSTRAINT `FK_DONATE_EXPO` FOREIGN KEY (`expo_id`) REFERENCES `EXPO` (`id`),
                CONSTRAINT `FK_DONATE_MEMBER` FOREIGN KEY (`member_id`) REFERENCES `MEMBER` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='贊助'
        ";

        if (!$mysqli->query($create_table)) {
            throw new Exception("建立資料表失敗: " . $mysqli->error);
        }
    }

    // 8-1 查出對應的 展覽名稱 與 artist_id
    $expo_name = '';
    $artist_id_db = 0;

    $stmt = $mysqli->prepare("SELECT name, artist_id FROM EXPO WHERE id = ?");
    $stmt->bind_param("i", $expo_id);
    $stmt->execute();
    $stmt->bind_result($expo_name_fetch, $artist_id_fetch);
    if ($stmt->fetch()) {
        $expo_name = $expo_name_fetch;
        $artist_id_db = $artist_id_fetch;
    } else {
        echo json_encode(['error' => '查無該展覽']);
        exit;
    }
    $stmt->close();

    // 8-2 查出對應的 member_id（用 email 查）
    $member_id = null;  // 預設為 null
    $stmt = $mysqli->prepare("SELECT id FROM MEMBER WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($member_id_fetch);
    if($stmt->fetch()) {
        $member_id = $member_id_fetch;
    }
    $stmt->close();

    // 10. 儲存訂單到資料庫 (寫入 DONATE 資料表)
    $share = 10;
    $stmt = $mysqli->prepare(
        "INSERT INTO DONATE
            (`donate_no`, `d_name`, `d_email`, `amount`, `share`, `status`, `created_at`, `member_id`, `artist_id`, `expo_id`)
         VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?, ?, ?)"
    );
    if (!$member_id) {
        $member_id = null;
    }
    $stmt->bind_param(
        "sssiiiii",
        $donate_no,
        $d_name,
        $email,
        $amount,
        $share,
        $member_id,
        $artist_id_db,
        $expo_id
    );
    if (!$stmt->execute()) {
        throw new Exception("訂單寫入失敗: " . $stmt->error);
    }
    $donate_id = $stmt->insert_id;
    $stmt->close();

    // 記錄 log
    error_log("新贊助訂單建立: ID={$donate_id}, donate_no={$donate_no}, Amount={$amount}");

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo $e->getMessage(); // <<==== 加這一行，前端會看到錯誤細節
    echo '<div style="padding: 20px; color: red; text-align: center;">';
    echo '<h3>系統錯誤</h3>';
    echo '<p>無法處理您的贊助請求，請稍後再試。</p>';
    echo '<button onclick="history.back()">返回</button>';
    echo '</div>';
    exit;
}

// ==================================== 綠界 ====================================

// 組訂單資訊
$order = [
    'MerchantID' => $merchantID,
    'MerchantTradeNo' => $donate_no, // 使用我們的訂單編號
    'MerchantTradeDate' => date('Y/m/d H:i:s'),
    'PaymentType' => 'aio',
    'TotalAmount' => (int)$amount, // 使用者實際輸入的金額
    'TradeDesc' => '緯藝TibaArt贊助', // . $expo_name, // 交易描述（必填）
    'ItemName' =>   $expo_name .' 贊助藝術家 ',  //          // 商品名稱（可加上 # 結尾）改為從前端取得的商品名稱字串
    // 'ReturnURL' => 'https://tibamef2e.com/tjd101/g2/api/returnDonate.php',
    'ReturnURL' => 'http://localhost/test2/returndonate.php',
    'ClientBackURL' => 'https://tibamef2e.com/tjd101/g2/front', // 使用者完成付款後會回到這頁
    'ChoosePayment' => 'Credit',
    'EncryptType' => 1,
    'Email' => $email
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
