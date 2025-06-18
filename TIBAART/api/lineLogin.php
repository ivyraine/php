<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

include('cross_domain.php');
include ('conn.php');


// 設定 LINE Developers 提供的資訊
// 這些應該從您的環境變數或安全配置中獲取，不應直接寫死在程式碼中
$line_client_id = '2007579702'; 
$line_client_secret = '5b739e47716172d61706444a063fe13a'; 
$line_redirect_uri = 'http://localhost:5173/tjd101/g2/front/LineCallback'; // 必須與 LINE Developers 中設定的一致，且與前端發送的一致

// 接收前端發送的 JSON 資料
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$code = $data['code'] ?? null;
$frontend_redirect_uri = $data['redirect_uri'] ?? null; // 前端傳來確認的回調 URI

// 簡單的驗證
if (empty($code) || $frontend_redirect_uri !== $line_redirect_uri) {
    echo json_encode(['success' => false, 'message' => '無效的請求或回調URI不匹配。']);
    exit();
}

// 步驟 1: 向 LINE 交換 Access Token
$token_url = 'https://api.line.me/oauth2/v2.1/token';
$post_fields = [
    'grant_type'    => 'authorization_code',
    'code'          => $code,
    'redirect_uri'  => $line_redirect_uri,
    'client_id'     => $line_client_id,
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

// 步驟 2: 解析 ID Token (JWT) 以獲取用戶資料
// 實際應用請使用成熟的 JWT 庫並驗證簽名 (例如 firebase/php-jwt)
$id_token_parts = explode('.', $id_token);
if (count($id_token_parts) !== 3) {
    echo json_encode(['success' => false, 'message' => '無效的 ID Token。']);
    exit();
}
$payload_encoded = $id_token_parts[1];
$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload_encoded)), true);

// 提取用戶資料，對應您的資料表欄位
$line_user_id = $payload['sub'] ?? null; // 對應 line_user_id 欄位
$line_display_name = $payload['name'] ?? null; 
$line_picture_url = $payload['picture'] ?? null; 
$line_email = $payload['email'] ?? null; // 對應 email 欄位

if (empty($line_user_id)) {
    echo json_encode(['success' => false, 'message' => '無法獲取 LINE 用戶 ID。']);
    exit();
}

// 步驟 3: 根據 LINE 用戶 ID 在您的資料庫中查詢或註冊用戶
$db_success = true;
$member_info = [];

try {

    // 檢查連線
    if ($conn->connect_error) {
        throw new Exception("資料庫連線失敗: " . $conn->connect_error);
    }

    // 查詢是否已存在 LINE 帳號綁定的會員
    $stmt = $conn->prepare("SELECT id, email, name, img, phone, gender, birthday, per, line_user_id FROM MEMBER WHERE line_user_id = ?");
    $stmt->bind_param("s", $line_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 會員已存在，直接登入
        $member_info_from_db = $result->fetch_assoc();
        
        // 更新會員資料 (例如：名稱、頭像可能已更新)
        $update_stmt = $conn->prepare("UPDATE MEMBER SET name = ?, img = ?, updated = CURRENT_TIMESTAMP WHERE line_user_id = ?");
        $update_stmt->bind_param("sss", $line_display_name, $line_picture_url, $line_user_id);
        $update_stmt->execute();

        $member_info = [
            'id' => $member_info_from_db['id'],
            'name' => $member_info_from_db['name'],
            'email' => $member_info_from_db['email'], // 保持資料庫中的 email
            'type' => ($member_info_from_db['per'] == 0) ? 'artist' : 'general', // 根據 per 欄位判斷會員類型
            'line_user_id' => $member_info_from_db['line_user_id'],
            // 如果需要，可以把 img, phone, gender, birthday 也傳回
            'img' => $member_info_from_db['img'],
            'phone' => $member_info_from_db['phone'],
            'gender' => $member_info_from_db['gender'],
            'birthday' => $member_info_from_db['birthday'],
            'message' => 'LINE 會員登入成功！'
        ];
    } else {
        // 會員不存在，註冊新會員
        $default_per = 1; // 一般會員
        $default_type = 'general'; // 提供給前端判斷的類型字串

        $stmt = $conn->prepare("INSERT INTO MEMBER (name, email, img, per, line_user_id) VALUES (?, ?, ?, ?, ?)");
        // sssis: name(string), email(string), img(string), per(integer), line_user_id(string)
        $stmt->bind_param("sssis", $line_display_name, $line_email, $line_picture_url, $default_per, $line_user_id);

        if ($stmt->execute()) {
            $member_info = [
                'id' => $conn->insert_id,
                'name' => $line_display_name,
                'email' => $line_email,
                'type' => $default_type,
                'line_user_id' => $line_user_id,
                'message' => 'LINE 會員註冊並登入成功！'
            ];
        } else {
            // 如果 email 欄位有 UQ 且 LINE 返回的 email 已存在，會導致此處插入失敗。
            // 您需要考慮這種情況的處理方式：是提示用戶綁定現有帳號，還是只用 line_user_id 區分。
            // 由於您的 email 欄位也有 UQ 且 `line_user_id` 也是 UQ，這裡的處理是根據 `line_user_id` 來查找。
            // 如果 LINE 返回的 email 在您現有會員中已存在但未綁定 LINE，則這裡會創建一個新會員並帶有重複的 email (如果 email UQ 則會報錯)。
            // 建議：如果 email 有 UQ，應在插入前檢查 email 是否已存在。
            // 若 email 已存在，提示用戶是否要綁定此 LINE 帳號到現有 email 帳號，而不是直接創建新帳號。
            // 這裡為了簡化，暫時假設 email 不會衝突，如果衝突會報錯。
            error_log("會員註冊失敗: " . $stmt->error);
            throw new Exception("會員註冊失敗: " . $stmt->error);
        }
    }
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log('資料庫操作錯誤: ' . $e->getMessage());
    $db_success = false;
    echo json_encode(['success' => false, 'message' => '資料庫錯誤，請稍後再試。']);
    exit();
}

// 步驟 4: 返回成功訊息和會員資料給前端
if ($db_success) {
    echo json_encode([
        'success' => true,
        'message' => $member_info['message'],
        'member_info' => [
            'id' => $member_info['id'],
            'name' => $member_info['name'],
            'email' => $member_info['email'],
            'type' => $member_info['type'], // 'general' 或 'artist'
            // 如果需要，可以把從資料庫讀取或新插入的其他欄位也傳回
            'img' => $member_info['img'] ?? $line_picture_url, // 如果資料庫中沒有，則使用 LINE 提供的
            'phone' => $member_info['phone'] ?? null,
            'gender' => $member_info['gender'] ?? null,
            'birthday' => $member_info['birthday'] ?? null,
            'line_user_id' => $member_info['line_user_id'],
        ]
    ]);
}
?>