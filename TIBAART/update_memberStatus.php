<?php
include ('cross_domain.php');
include('conn.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$per = $data['per'];

$sql = "UPDATE MEMBER SET per = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$per, $id]);

echo json_encode(['success' => true]);
?>