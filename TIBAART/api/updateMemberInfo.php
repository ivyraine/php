<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// 1. 建立連線
// $db_host = "127.0.0.1";
// $db_user = "root";
// $db_pass = "password";
// $db_select = "TIBAART";

// Tibame
$db_host = "127.0.0.1";
$db_user = "tibamefe_since2021";
$db_pass = "vwRBSb.j&K#E";
$db_select = "tibamefe_tjd101g2";

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

$id = $member['id'];
$type = $member['type'];
$name = $member['name'] ?? '';
$phone = $member['phone'] ?? '';
$birthday = $member['birthday'] ?? '';
$gender = $member['gender'] ?? '';
$password = $member['password'] ?? '';
$bank_account = $member['bank_account'] ?? null;


try {
    if ($type === 'general') {
        // 判斷是否需要更新密碼
        if (!empty($password)) {
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE MEMBER SET name = ?, phone = ?, birthday = ?, gender = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $birthday, $gender, $hashedPwd, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE MEMBER SET name = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $birthday, $gender, $id]);
        }
    } elseif ($type === 'artist') {
        if (!empty($password)) {
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE ARTIST SET name = ?, phone = ?, birthday = ?, gender = ?, bank_account = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $birthday, $gender, $bank_account, $hashedPwd, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE ARTIST SET name = ?, phone = ?, birthday = ?, gender = ?, bank_account = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $birthday, $gender, $bank_account, $id]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '無效的會員類型']);
        exit;
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫錯誤：' . $e->getMessage()]);
}
?>