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


$dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗: ' . $e->getMessage()]);
    exit;
}

// 2. 解析前端送來的 JSON
$member = json_decode(file_get_contents("php://input"), true);
if (!$member) {
    echo json_encode(['success' => false, 'message' => '無法解析 JSON']);
    exit;
}

// 3. 檢查必要欄位
if (empty($member['email']) || empty($member['password'])) {
    echo json_encode(['success' => false, 'message' => '欄位不完整']);
    exit;
}

// 4. 寫入資料
try {

    $hashed_password = password_hash($member['password'], PASSWORD_DEFAULT);

    $sql = '
        INSERT INTO MEMBER (name, email, password, phone, birthday, gender)
        VALUES (:name, :email, :password, :phone, :birthday, :gender)
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $member['name']);
    $stmt->bindValue(':email', $member['email']);
    // $stmt->bindValue(':password', $member['password']);
    $stmt->bindValue(':password', $hashed_password);
    $stmt->bindValue(':phone', $member['phone']);
    $stmt->bindValue(':birthday', $member['birthday']);
    $stmt->bindValue(':gender', $member['gender']);

    $stmt->execute();
    $id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => '註冊成功',
        'id' => $id
    ]);
    exit;

} catch (PDOException $e) {
    // 例如信箱重複可能會導致 SQL error
    echo json_encode([
        'success' => false,
        'message' => 'SQL 錯誤：' . $e->getMessage()
    ]);
    exit;
}
