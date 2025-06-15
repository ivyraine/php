<?php
include('cross_domain.php'); 
include('conn.php'); 

header('Content-Type: application/json'); 

// 1. 檢查請求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => '無效的請求方法，只接受 POST 請求。'
    ]);
    exit();
}

// 2. 解析前端傳來的 JSON 資料
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// 檢查 JSON 解析是否成功
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => '無效的 JSON 資料格式。',
        'json_error' => json_last_error_msg()
    ]);
    exit();
}

// 3. 提取並驗證會員 ID
$member_id = $data['member_id'] ?? null;

if (empty($member_id)) {
    echo json_encode([
        'success' => false,
        'message' => '會員 ID 為空或無效。'
    ]);
    exit();
}

// 4. 查詢資料庫獲取會員訂單
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 查 ORDERS 查會員的訂單
    $stmt_orders = $pdo->prepare("
        SELECT
            id,
            order_number,
            order_date,
            total_amount,
            status
        FROM
            ORDERS
        WHERE
            member_id = :member_id
        ORDER BY
            order_date DESC
    ");
    $stmt_orders->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt_orders->execute();
    $orders_data = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    $formatted_orders = [];

    // 查詢商品細節
    foreach ($orders_data as $order) {
        $stmt_order_details = $pdo->prepare("
            SELECT
                product_name AS name,
                quantity,
                unit_price
            FROM
                ORDER_DETAIL
            WHERE
                order_id = :id
        ");
        $stmt_order_details->bindParam(':id', $order['id'], PDO::PARAM_INT);
        $stmt_order_details->execute();
        $items = $stmt_order_details->fetchAll(PDO::FETCH_ASSOC);

        // 資料庫沒有規格的欄位 但還是留著以備不時之需
        foreach ($items as &$item) {
            $item['spec'] = 'N/A'; 
        }
        unset($item); // 解除引用，避免潛在問題

        $formatted_orders[] = [
            'orderId' => $order['order_number'], 
            'items' => $items,
            'orderAmount' => (int)$order['total_amount'], 
            'orderTime' => $order['order_date'],
            'status' => $order['status']
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => '成功獲取訂單資料。',
        'orders' => $formatted_orders
    ]);

} catch (PDOException $e) {
    error_log("Database error in getMemberOrders.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => '資料庫查詢失敗，請稍後再試。' . $e->getMessage() // 正式環境建議隱藏 $e->getMessage()
    ]);
}

exit();
?>