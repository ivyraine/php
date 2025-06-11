<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

$host = 'localhost';
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

    // 獲取展覽 ID
    $expoId = $_GET['expo_id'] ?? null;
    
    if (!$expoId) {
        throw new Exception('展覽 ID 不能為空');
    }

    // 查詢該展覽的所有作品
    // 假設你有一個 ARTWORK 表，並且有 expo_id 欄位關聯到展覽
    $stmt = $pdo->prepare("SELECT * FROM ARTWORK WHERE expo_id = ? ORDER BY id");
    $stmt->execute([$expoId]);
    $data = $stmt->fetchAll();

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