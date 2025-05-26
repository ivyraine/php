<?php

$id = $_GET['id'];

include 'connection.php';

$sql = 'select avatar from MEMBER where ID = :id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id);
$stmt->execute();

$row= $stmt->fetch();

header('Content-Type: image/png');
echo $row['avatar'];




?>