<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// 1. 建立連線
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "password";
$db_select = "TIBAART";

// Tibame
// $db_host = "127.0.0.1";
// $db_user = "tibamefe_since2021";
// $db_pass = "vwRBSb.j&K#E";
// $db_select = "tibamefe_tjd101g2";

$dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8mb4";
// $dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8";

$options = [
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗: ' . $e->getMessage()]);
    exit;
}

$member = json_decode(file_get_contents("php://input"), true);
if (!$member) {
    echo json_encode(['success' => false, 'message' => '無法解析 JSON']);
    exit;
}

$type = $member['type'] ?? 'general';
$id = $member['id'] ?? null;

if (!isset($member['id'])) {
  echo json_encode(['success' => false, 'message' => '缺少會員 ID']);
  exit;
}

if ($type === 'general') {
    $stmt = $pdo->prepare('SELECT id, email, name, phone, birthday, gender FROM MEMBER WHERE id = :id');
} else if ($type === 'artist') {
    $stmt = $pdo->prepare('SELECT id, account, email, name, phone, birthday, gender, bank_account FROM ARTIST WHERE id = :id');
} else {
    echo json_encode(['success' => false, 'message' => '無效的會員類型']);
    exit;
}

try {
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
       $memberInfo = [
        'id' => base64_encode($user['id']),
        'email' => base64_encode($user['email']),
        'name' => $user['name'],
        'phone' => base64_encode($user['phone']),
        'birthday' => base64_encode($user['birthday']),
        'gender' => base64_encode($user['gender']),
    ];

    // 如果是藝術家才回傳 bank_account
        if ($type === 'artist' && isset($user['bank_account'])) {
            $memberInfo['bank_account'] = base64_encode($user['bank_account']);
        }

    echo json_encode([
        'success' => true,
        'member_info' => $memberInfo
    ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '查無此會員'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'SQL 錯誤：' . $e->getMessage()
    ]);
}
