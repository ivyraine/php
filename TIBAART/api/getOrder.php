<?php

include('cross_domain.php');
include ('conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => '僅接受 GET 請求。'
    ]);
    exit();
}

session_start();
$member_id = $_SESSION['member_id'] ?? null;
$order_number = $_GET['order_number'] ?? null;

if (empty($member_id) || empty($order_number)) {
    echo json_encode([
        'success' => false,
        'message' => '請提供會員 ID 和訂單編號。'
    ]);
    exit();
}

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT order_number 
        FROM ORDERS 
        WHERE member_id = ? AND order_number = ?
    ");
    $stmt->execute([$member_id, $order_number]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo json_encode([
            'success' => true,
            'orderNumber' => $order['order_number']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '訂單不存在或不屬於該會員。'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '查詢失敗，請稍後再試。'
    ]);
    error_log("Database error: " . $e->getMessage());
}
?>