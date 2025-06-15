<?php
  
  include('cross_domain.php');
  include ('conn.php');
        
  //---------------------------------------------------

  $expo = json_decode(file_get_contents("php://input"), true);

  // 檢查必要欄位
if (
    empty($expo['name']) ||
    empty($expo['name_en']) ||
    empty($expo['start']) ||
    empty($expo['end']) ||
    empty($expo['note']) ||
    empty($expo['artist_id']) ||
    empty($expo['cover'])
) {
    http_response_code(400);
    echo json_encode(['error' => '缺少必要欄位']);
    exit;
}

$start = empty($expo['start']) ? null : $expo['start'];
$end = empty($expo['end']) ? null : $expo['end'];

$sql = "
    INSERT INTO EXPO (name, name_en, start, end, note, artist_id, cover)
    VALUES (:name, :name_en, :start, :end, :note, :artist_id, :cover)
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $expo['name']);
$stmt->bindValue(':name_en', $expo['name_en']);
$stmt->bindValue(':start', $start);
$stmt->bindValue(':end', $end);
$stmt->bindValue(':note', $expo['note']);
$stmt->bindValue(':artist_id', $expo['artist_id']);
$stmt->bindValue(':cover', $expo['cover']);

// 執行資料庫操作
try {
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// file_put_contents('debug.log', print_r($artwork, true));