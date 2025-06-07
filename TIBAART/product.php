<?php
// 啟用錯誤顯示（僅用於除錯，正式環境應關閉）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 設置 CORS 和 Content-Type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

$db_host = "127.0.0.1";
// $db_host = "localhost";
$db_user = "root";
$db_pass = "password";
$db_select = "TIBAART";

$dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 查詢 product 表，只取得狀態為啟用的商品
    $stmt = $pdo->query("SELECT 
        id,
        name,
        img as image,
        type as category,
        des as description,
        price,
        stock,
        updated,
        status
    FROM product 
    WHERE status = 1 
    ORDER BY updated DESC");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 檢查是否無資料
    if (empty($products)) {
        echo json_encode([
            'error' => true,
            'message' => '沒有找到符合條件的商品'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 轉換資料格式
    $formattedProducts = array_map(function($product) {
        return [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'category' => $product['category'],
            'description' => $product['description'],
            'price' => (int)$product['price'],
            'stock' => (int)$product['stock'],
            'updated' => $product['updated'],
            'status' => (int)$product['status']
        ];
    }, $products);

    echo json_encode($formattedProducts, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => '資料庫連線失敗: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

// echo json_encode(["status" => "success"]);

?>