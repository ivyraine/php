<?php
//把前端傳來的字串 decode(解碼)
$member = json_decode(file_get_contents("php://input"), true);



//MySQL相關資訊
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "password";
$db_select = "EXAMPLE";
//建立資料庫連線物件
$dsn = "mysql:host=" . $db_host . ";dbname=" . $db_select . ";charset=utf8";
//建立PDO物件，並放入指定的相關資料
$pdo = new PDO($dsn, $db_user, $db_pass);

$sql = '
    select *
    from member
    where username = :usr and password = :pwd
';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':usr', $member['username']);
$stmt->bindValue(':pwd', $member['password']);
$stmt->execute();

$result = $stmt->fetch();

// echo $member['username'];
// echo $member['password'];

$member['pass'] = true;
echo json_encode($result);

?>





