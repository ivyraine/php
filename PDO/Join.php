<?php

echo $_POST["account"];
echo "<br>";
echo $_POST["password"];


//MySQL相關資訊
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "password";
$db_select = "pdo";

//建立資料庫連線物件
$dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

//建立PDO物件，並放入指定的相關資料
$pdo = new PDO($dsn, $db_user, $db_pass);

//---------------------------------------------------

//建立SQL
$sql = "INSERT INTO member(Account, PWD, CreateDate) VALUES ('".$_POST["account"]."', '".$_POST["password"]."', NOW())";

//執行
$affectedRow = $pdo->exec($sql);
if($affectedRow > 0){
        echo "新增成功!";
}else{
        echo "新增失敗!";
}

//PHP轉址到原本PDO範例中的Select.php
header("Location: Select.php");

?>