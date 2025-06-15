<?php

include('cross_domain.php');
include ('conn.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 查 product
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
    ORDER BY RAND()");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 檢查是否無資料
    if (empty($products)) {
        echo json_encode([
            'error' => true,
            'message' => '沒有找到符合條件的商品'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 轉換格式
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

?>