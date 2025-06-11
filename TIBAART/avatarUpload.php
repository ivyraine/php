<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// 1. 建立連線
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "password";
$db_select = "TIBAART";

// Tibame
// $db_host = "127.0.0.1";
// $db_user = "tibamefe_since2021";
// $db_pass = "vwRBSb.j&K#E";
// $db_select = "tibamefe_tjd101g2";

$dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8mb4";
// $dsn = "mysql:host=$db_host;dbname=$db_select;charset=utf8";

$options = [
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗: ' . $e->getMessage()]);
    exit;
}

$id = $_POST['id'];
$type = $_POST['type'];
$file = $_FILES['img']; 


if (!$id || !isset($_FILES['img'])) {
    echo json_encode(['success' => false, 'message' => '缺少必要資料']);
    exit;
}

// localhost/TibaArt/public/api/xxx.php
// /txx104/g2/api/xxx.php

// $uploadDir = dirname(__DIR__) . '/TibaUpload/';
$uploadDir = __DIR__ . '/AvatarUpload/';
// $ServerRoot = $_SERVER["DOCUMENT_ROOT"];
// $uploadDir = $ServerRoot."/TibaUpload/";
// echo $uploadDir

// 網站可訪問的路徑，用於資料庫與前端顯示
$uploadPathForDB = 'TIBAART/AvatarUpload/'; 
// $filename = $_FILES["img"]["name"]; 

$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_') . '.' . $ext;
$targetPath = $uploadDir . $filename; // 實體路徑
$targetPathForDB = $uploadPathForDB . $filename; // 資料庫用相對路徑

// echo $targetPath

if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
    // 更新資料庫時使用相對路徑
    $stmt = null;
    if ($type === 'general') {
        $stmt = $pdo->prepare("UPDATE MEMBER SET img = ? WHERE id = ?");
    } else if ($type === 'artist') {
        $stmt = $pdo->prepare("UPDATE ARTIST SET img = ? WHERE id = ?");
    }

    if ($stmt) {
        $stmt->execute([$targetPathForDB, $id]);  // 用相對路徑存資料庫
        echo json_encode(['success' => true, 'url' => $targetPathForDB]);
    } else {
        echo json_encode(['success' => false, 'message' => '無效的會員類型']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '圖片上傳失敗']);
}


?>