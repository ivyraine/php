<?php
$plainPassword = 'yoyoyo'; // 換成你要加密的密碼
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hashedPassword;