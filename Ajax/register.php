<?php
//把前端傳來的字串(json) decode(解碼)
$member = json_decode(file_get_contents("php://input"), true);


include 'connection.php';

$sql = '
    insert into MEMBER(USERNAME, PASSWORD, NICKNAME,AVATAR)
    value(:username, :password, :nickname, :avatar)
';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $member['username']);
$stmt->bindValue(':password', $member['password']);
$stmt->bindValue(':nickname', $member['nickname']);
$stmt->bindValue(':avatar', base64_decode($member['avatar']));
$stmt->execute();

$id = $pdo->lastInsertId();
$respBody['id'] = $id;

echo json_encode($respBody);

?>





