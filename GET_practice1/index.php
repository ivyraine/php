<?php

echo "姓名: ".$_GET["name"];
echo "<br>";

echo "性別: ";
    if(isset($_GET["gender"])) {
        echo $_GET["gender"];
    }else{
        echo "未填寫";
    } 

echo "<br>";

echo "交通工具: ";
   if(isset($_GET["mobile"]) && is_array($_GET["mobile"])) {
    foreach($_GET["mobile"] as $key => $value) {
        echo $value;
        if($key < count($_GET["mobile"]) - 1) {
            echo ", ";
        }
    }
}else{
        echo "未填寫";
    } 

?>


<!-- http://localhost/GET_practice1/index.php
?
name=Ivy
&
gender=%E5%A5%B3
&
mobile%5B%5D=%E6%B1%BD%E8%BB%8A
&
mobile%5B%5D=%E6%A9%9F%E8%BB%8A -->