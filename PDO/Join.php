<?php

// echo $_POST["account"];
// echo "<br>";
// echo $_POST["password"];

include("conn.php");

//---------------------------------------------------

$account = htmlspecialchars($_POST["account"]);
$password = htmlentities($_POST["password"]);

//建立SQL
$sql = "INSERT INTO member(Account, PWD, CreateDate) VALUES (?, ?, NOW())";

//執行
// $affectedRow = $pdo->exec($sql);
$statement = $pdo->prepare($sql); 
$statement->bindValue(1, $account);
$statement->bindValue(2, $password);
$affectedRow = $statement->execute();

if($affectedRow > 0){
        //echo "新增成功!";
        //PHP轉址到原本PDO範例中的Select.php
        header("Location: Select.php");
}else{
        echo "新增失敗!";
}

?>