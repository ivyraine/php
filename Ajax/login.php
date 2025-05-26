<?php
//把前端傳來的字串 decode(解碼)
$member = json_decode(file_get_contents("php://input"), true);

include 'connection.php';

$sql = '
    select id, username, nickname, avatar
    from member
    where username = :usr and password = :pwd
';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':usr', $member['username']);
$stmt->bindValue(':pwd', $member['password']);
$stmt->execute();

$result = $stmt->fetch();
$respBody['id'] = $result['id'];
$respBody['username'] = $result['username'];
$respBody['nickname'] = $result['nickname'];
$respBody['avatar'] = base64_encode($result['avatar']);

echo json_encode($respBody);

?>

