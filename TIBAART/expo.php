<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$host = 'localhost';
$db   = 'TIBAART';
$user = 'root';
$pass = 'password';  // 請改為你的實際密碼
$charset = 'utf8mb4';  // 改為 utf8mb4 支援更完整的中文

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 查詢所有展覽資料，按開始日期倒序排列
    $stmt = $pdo->query("SELECT * FROM EXPO ORDER BY start DESC");
    $data = $stmt->fetchAll();

    // 為每個展覽項目加上前端需要的額外屬性
    $processedData = [];
    foreach ($data as $index => $item) {
        $processedData[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'name_en' => $item['name_en'],
            'cover' => $item['cover'],
            'start' => $item['start'],
            'end' => $item['end'],
            'note' => $item['note'],
            'status' => $item['status'],
            'artist_id' => $item['artist_id'],
            // 前端需要的額外屬性
            'titleZh' => $item['name'],
            'img' => $item['cover'],
            'to' => '/front/ExpoArea/${item.id}',  // 路由路徑
            'className' => 'grid div' . ($index + 1),  // CSS 類名
            'side' => ($index % 2 === 0) ? 'left' : 'right'  // 左右交替
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $processedData
    ], JSON_UNESCAPED_UNICODE);  // 確保中文正確顯示

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}