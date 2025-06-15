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

$artistId = $_GET['id'] ?? '';

if (!$artistId) {
    echo json_encode(['success' => false, 'message' => '缺少會員 ID']);
    exit;
}

$stmt = $pdo->prepare('
    SELECT 
        a.id AS artist_id,
        e.id AS expo_id,
        e.name AS expo_name,
        e.start,
        e.end,
        e.status,
        COALESCE(ROUND(SUM(d.amount * 0.9)), 0) AS total_donation
    FROM ARTIST a
    JOIN EXPO e ON e.artist_id = a.id
    LEFT JOIN DONATE d ON d.expo_id = e.id
    WHERE a.id = :id
    GROUP BY e.id
    ORDER BY e.start DESC
');

try {
    $stmt->bindValue(':id', $artistId);
    $stmt->execute();
    $expos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo json_encode([
    //     'success' => true,
    //     'expo' => $expos
    // ]);
    echo json_encode($expos);


} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'SQL 錯誤：' . $e->getMessage()
    ]);
}
