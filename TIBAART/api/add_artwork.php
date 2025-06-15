<?php
  
  include('cross_domain.php');
  include ('conn.php');
        
  //---------------------------------------------------

  $artwork = json_decode(file_get_contents("php://input"), true);

  // 檢查必要欄位
if (
    empty($artwork['name']) ||
    empty($artwork['name_en']) ||
    empty($artwork['stuff']) ||
    empty($artwork['size']) ||
    empty($artwork['note']) ||
    empty($artwork['artist_id']) ||
    empty($artwork['img'])
) {
    http_response_code(400);
    echo json_encode(['error' => '缺少必要欄位']);
    exit;
}

$sql = "
    INSERT INTO ARTWORK (name, name_en, stuff, size, note, artist_id, img)
    VALUES (:name, :name_en, :stuff, :size, :note, :artist_id, :img)
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $artwork['name']);
$stmt->bindValue(':name_en', $artwork['name_en']);
$stmt->bindValue(':stuff', $artwork['stuff']);
$stmt->bindValue(':size', $artwork['size']);
$stmt->bindValue(':note', $artwork['note']);
$stmt->bindValue(':artist_id', $artwork['artist_id']);
$stmt->bindValue(':img', $artwork['img']);

// 執行資料庫操作
try {
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// file_put_contents('debug.log', print_r($artwork, true));