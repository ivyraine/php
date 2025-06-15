<?php
include ('cross_domain.php');
include('conn.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$per = $data['per'];
$type = strtolower($data['type']);  //大寫會錯誤，需轉成小寫

$table = '';
if($type === 'member'){
  $table = 'MEMBER';
}elseif($type === 'artist'){
  $table = 'ARTIST';
}else{
  http_response_code(400);
  echo json_encode(['error' => '無效的 type，請使用 member 或 artist']);
  exit;
}


$sql = "UPDATE $table SET per = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$per, $id]);

echo json_encode(['success' => true]);

file_put_contents('debug.log', print_r($data, true));
?>