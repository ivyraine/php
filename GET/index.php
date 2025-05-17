<?php

// echo $_GET["account"];

//防呆
if(isset($_GET["accountt"])){
    echo $_GET["accountt"];
}else{
    echo "參數錯誤，請洽客服!";
} 

//if-else很囉唆 可以改成 三元運算子
// echo isset($_GET["account"]) ? $_GET["account"] : "";

// echo "<br>";
// echo $_GET["password"];



?>

<br>
<!-- 
http://localhost/GETPOST/index.php
?
account=tabame123
&
password=1234 -->