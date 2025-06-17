<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($_SERVER['REQUEST_METHOD']==='OPTIONS') {
  http_response_code(200);
  exit;
}

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

    // 獲取作品 ID
    $artworkId = $_GET['id'] ?? null;
    
    if (!$artworkId) {
        throw new Exception('作品 ID 不能為空');
    }

    // 查詢單一作品的詳細資料
    $stmt = $pdo->prepare("SELECT 
                        AW.*,
                        E.name AS expo_name,
                        E.id AS expo_id
                        FROM 
                        ARTWORK AS AW
                        LEFT JOIN EXPO_ARTWORK AS EA ON AW.id = EA.artwork_id
                        LEFT JOIN EXPO AS E ON EA.expo_id = E.id
                        WHERE 
                        AW.id = :artwork_id");

    $stmt->bindValue(':artwork_id', $artworkId, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();

    if (!$data) {
        throw new Exception('找不到指定的作品');
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