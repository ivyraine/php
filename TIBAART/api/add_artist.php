<?php
  include('cross_domain.php');
  include ('conn.php');
        
  //---------------------------------------------------

  $artist = json_decode(file_get_contents("php://input"), true);

  // 檢查必要欄位
if (
    empty($artist['account']) ||
    empty($artist['password']) ||
    empty($artist['email']) ||
    empty($artist['name']) ||
    empty($artist['phone']) ||
    empty($artist['bank_account']) ||
    empty($artist['intro'])
) {
    http_response_code(400);
    echo json_encode(['error' => '缺少必要欄位']);
    exit;
}

// 預設值處理
$gender = $artist['gender'] ?? 'Other';
$birthday = empty($artist['birthday']) ? null : $artist['birthday'];
// 加密密碼
$hashedPassword = password_hash($artist['password'], PASSWORD_DEFAULT);

$sql = "
    INSERT INTO ARTIST (account, password, email, name, phone, bank_account, gender, birthday, intro)
    VALUES (:account, :password, :email, :name, :phone, :bank_account, :gender, :birthday, :intro)
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':account', $artist['account']);
$stmt->bindValue(':password', $hashedPassword);
$stmt->bindValue(':email', $artist['email']);
$stmt->bindValue(':name', $artist['name']);
$stmt->bindValue(':phone', $artist['phone']);
$stmt->bindValue(':bank_account', $artist['bank_account']);
$stmt->bindValue(':gender', $gender);
$stmt->bindValue(':birthday', $birthday);
$stmt->bindValue(':intro', $artist['intro']);

// 執行資料庫操作
try {
    $stmt->execute();
    $id = $pdo->lastInsertId();
    echo json_encode(['id' => $id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// file_put_contents('debug.log', print_r($artist, true));
?>