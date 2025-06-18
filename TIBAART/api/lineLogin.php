<?php
header('Content-Type: application/json');
include('cross_domain.php');
include('conn.php');

// LINE 開發者資訊
$line_client_id = '2007579702'; 
$line_client_secret = '5b739e47716172d61706444a063fe13a'; 
$line_redirect_uri = 'http://localhost:5173/tjd101/g2/front/LineCallback'; 
// $line_redirect_uri = 'https://tibamef2e.com/tjd101/g2/front/LineCallback'; 

// $line_client_id = $_ENV['VITE_LINE_CLIENT_ID']; 
// $line_client_secret = $_ENV['VITE_LINE_CLIENT_SECRET']; 
// $line_redirect_uri = $_ENV['VITE_LINE_REDIRECT_URI'];

// 取得前端傳來的 code 與 redirect_uri
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$code = $data['code'] ?? null;
$frontend_redirect_uri = $data['redirect_uri'] ?? null;

if (empty($code) || $frontend_redirect_uri !== $line_redirect_uri) {
    echo json_encode(['success' => false, 'message' => '無效的請求或回調URI不匹配。']);
    exit();
}

// 步驟 1: 換取 token
$token_url = 'https://api.line.me/oauth2/v2.1/token';
$post_fields = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $line_redirect_uri,
    'client_id' => $line_client_id,
    'client_secret' => $line_client_secret,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
]);

$token_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token_data = json_decode($token_response, true);

if ($http_code !== 200 || !isset($token_data['access_token'])) {
    error_log('LINE Token 交換失敗: ' . $token_response);
    echo json_encode(['success' => false, 'message' => 'LINE 授權碼交換失敗。']);
    exit();
}

$access_token = $token_data['access_token'];
$id_token = $token_data['id_token'];

// 步驟 2: 解析 ID Token
$id_token_parts = explode('.', $id_token);
if (count($id_token_parts) !== 3) {
    echo json_encode(['success' => false, 'message' => '無效的 ID Token。']);
    exit();
}
$payload_encoded = $id_token_parts[1];
$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload_encoded)), true);

// 取得用戶資訊
$line_user_id = $payload['sub'] ?? null;
$line_display_name = $payload['name'] ?? null;
$line_picture_url = $payload['picture'] ?? null;
$line_email = $payload['email'] ?? null;

if (empty($line_user_id)) {
    echo json_encode(['success' => false, 'message' => '無法獲取 LINE 用戶 ID。']);
    exit();
}

// 步驟 3: 查詢或註冊會員
try {
    $stmt = $pdo->prepare("SELECT * FROM MEMBER WHERE line_user_id = ?");
    $stmt->execute([$line_user_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        // 已註冊 → 更新資料
        $update = $pdo->prepare("UPDATE MEMBER SET name = ?, img = ?, updated = CURRENT_TIMESTAMP WHERE line_user_id = ?");
        $update->execute([$line_display_name, $line_picture_url, $line_user_id]);

        $response = [
            'success' => true,
            'message' => 'LINE 會員登入成功！',
            'member_info' => [
                'id' => $member['id'],
                'name' => $member['name'],
                'email' => $member['email'],
                'type' => ($member['per'] == 0) ? 'artist' : 'general',
                'img' => $member['img'],
                'phone' => $member['phone'],
                'gender' => $member['gender'],
                'birthday' => $member['birthday'],
                'line_user_id' => $member['line_user_id']
            ]
        ];
    } else {
        // 尚未註冊 → 新增會員
        $default_per = 1;
        $stmt = $pdo->prepare("INSERT INTO MEMBER (name, email, img, per, line_user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$line_display_name, $line_email, $line_picture_url, $default_per, $line_user_id]);
        $new_id = $pdo->lastInsertId();

        $response = [
            'success' => true,
            'message' => 'LINE 會員註冊並登入成功！',
            'member_info' => [
                'id' => $new_id,
                'name' => $line_display_name,
                'email' => $line_email,
                'type' => 'general',
                'img' => $line_picture_url,
                'phone' => null,
                'gender' => null,
                'birthday' => null,
                'line_user_id' => $line_user_id
            ]
        ];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    error_log("資料庫錯誤: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => '資料庫錯誤，請稍後再試。']);
}
?>
