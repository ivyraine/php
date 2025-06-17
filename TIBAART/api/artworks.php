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

    // 獲取展覽 ID
    $expoId = $_GET['expo_id'] ?? null;
    
    if (!$expoId) {
        throw new Exception('展覽 ID 不能為空');
    }

    // 查詢該展覽的所有作品
    // 假設你有一個 ARTWORK 表，並且有 expo_id 欄位關聯到展覽
    $stmt = $pdo->prepare("SELECT
                        AW.*,          -- 選取 ARTWORK 表的所有欄位
                        E.name AS expo_name -- 選取展覽名稱
                        FROM
                        EXPO_ARTWORK AS EA
                        JOIN
                        ARTWORK AS AW ON EA.artwork_id = AW.id
                        JOIN
                        EXPO AS E ON EA.expo_id = E.id
                        WHERE
                        EA.expo_id = :expo_id ORDER BY id;");

   $stmt->bindValue(':expo_id', $expoId, PDO::PARAM_INT);

    $stmt->execute();
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