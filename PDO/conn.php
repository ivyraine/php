<?php
//function getPDO(){
    //MySQL相關資訊
    $db_host = "127.0.0.1";
    $db_user = "root";
    $db_pass = "password";
    $db_select = "pdo";

    //建立資料庫連線物件
    $dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

    //建立PDO物件，並放入指定的相關資料
    $pdo = new PDO($dsn, $db_user, $db_pass);
//}


// function getPDO2(){

//     $db_host = "127.0.0.9";
//     $db_user = "root";
//     $db_pass = "password";
//     $db_select = "pdo2";

//     $dsn = "mysql:host=".$db_host.";dbname=".$db_select.";charset=utf8";

//     $pdo = new PDO($dsn, $db_user, $db_pass);
// }
?>