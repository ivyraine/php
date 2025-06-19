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

$memberId = $_GET['id'] ?? '';

if (!$memberId) {
    echo json_encode(['success' => false, 'message' => '缺少會員 ID']);
    exit;
}

$stmt = $pdo->prepare('
    SELECT 
        e.name AS expo_name,
        d.amount AS donation,
        d.created_at AS donation_date
    FROM DONATE d 
    JOIN EXPO e ON d.expo_id = e.id
    WHERE d.member_id = :id
');

try {
    $stmt->bindValue(':id', $memberId);
    $stmt->execute();
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($donations);


} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'SQL 錯誤：' . $e->getMessage()
    ]);
}
