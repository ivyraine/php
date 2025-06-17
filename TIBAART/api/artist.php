<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

$host = '127.0.0.1';
$db   = 'TIBAART';
$user = 'root';
$pass = 'password';  // 請改為你的實際密碼
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 獲取藝術家 ID
    $artistId = $_GET['id'] ?? null;
    
    if (!$artistId) {
        throw new Exception('藝術家 ID 不能為空');
    }

    // 查詢特定藝術家資料
    $stmt = $pdo->prepare("SELECT * FROM ARTIST WHERE id = ?");
    $stmt->execute([$artistId]);
    $data = $stmt->fetch();

    if (!$data) {
        throw new Exception('找不到對應的藝術家資料');
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => "資料庫錯誤：" . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>