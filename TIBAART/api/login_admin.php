<?php

// file_put_contents('log.txt', file_get_contents("php://input"));
  include('cross_domain.php');
  include ('conn.php');

  //---------------------------------------------------

$data = json_decode(file_get_contents("php://input"), true);

// 取得帳號密碼
$account = $data['account'] ?? '';
$password = $data['password'] ?? '';

// 準備 SQL 查詢
$sql = "SELECT account, password FROM ADMIN WHERE account = :acc AND password = :pwd";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":acc", $account);
$stmt->bindParam(":pwd", $password);
$stmt->execute();

// 抓出資料
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // 登入成功
    echo json_encode([
        "success" => true,
        "account" => $result['account']
    ]);
} else {
    // 登入失敗
    echo json_encode([
        "success" => false,
        "message" => "帳號或密碼錯誤"
    ]);
}
?>