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

// 2. 解析前端送來的 JSON
$member = json_decode(file_get_contents("php://input"), true);
if (!$member) {
    echo json_encode(['success' => false, 'message' => '無法解析 JSON']);
    exit;
}

$type = $member['type'] ?? 'general'; // 預設一般會員

// 3. 檢查必要欄位
if ($type === 'general') {
    if (empty($member['email']) || empty($member['password'])) {
        echo json_encode(['success' => false, 'message' => '欄位不完整']);
        exit;
    }
    $identifier = $member['email'];
    $password = $member['password'];
    $sql = 'SELECT id, email, password, name, img, phone, birthday, gender FROM MEMBER WHERE email = :identifier';
} else if ($type === 'artist') {
    if (empty($member['account']) || empty($member['password'])) {
        echo json_encode(['success' => false, 'message' => '欄位不完整']);
        exit;
    }
    $identifier = $member['account'];
    $password = $member['password'];
    $sql = 'SELECT id, account, password, email, name, img, phone, birthday, gender, bank_account FROM ARTIST WHERE account = :identifier';
} else {
    echo json_encode(['success' => false, 'message' => '無效的會員類型']);
    exit;
}

// 4. 查詢資料
try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':identifier', $identifier);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        echo json_encode([
        'success' => false,
        'message' => '查無此帳號'
        ]);
        exit;
    }
    else if (!password_verify($password, $user['password'])){
    // else if ($user['password'] !== $password){
        echo json_encode([
        'success' => false,
        'message' => '密碼錯誤'
        ]);
        exit;
    } else if ($user && password_verify($password, $user['password'])) {
    // } else if ($user && $user['password'] === $password) {

        $memberInfo = [
            'id' => base64_encode($user['id']),
            'email' => base64_encode($user['email']),
            'password' => base64_encode($user['password']),
            'name' => base64_encode($user['name']),
            'img' => base64_encode($user['img']),
            'phone' => base64_encode($user['phone']),
            'birthday' => base64_encode($user['birthday']),
            'gender' => base64_encode($user['gender']),
            'type' => $type
        ];

        if ($type === 'artist') {
            $memberInfo['account'] = base64_encode($user['account']);
            $memberInfo['bank_account'] = base64_encode($user['bank_account']);
        }

        // 登入成功，回傳非敏感資訊
        echo json_encode([
            'success' => true,
            'message' => '登入成功',
            'member_info' => $memberInfo
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'SQL 錯誤：' . $e->getMessage()
    ]);
    exit;
}
