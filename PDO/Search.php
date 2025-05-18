<?php

    include("conn.php");

    //---------------------------------------------------

    $account = $_POST["account"];

    //bindValue() - 問號表示
    $sql = "SELECT * FROM member WHERE Account like ?";

    $statement = $pdo->prepare($sql); 
    $statement->bindValue(1, $account);
    $statement->execute();

    //抓出全部且依照順序封裝成一個二維陣列
    $data = $statement->fetchAll();

    if(count($data) > 0){
        echo "會員資料:";
        echo "<br>";
        
        //將二維陣列取出顯示其值
        foreach($data as $index => $row){
            echo $row["Account"];   //欄位名稱
            echo " / ";
            echo $row["PWD"];    //欄位名稱
            echo " / ";
            echo $row["CreateDate"];    //欄位名稱
                echo "<br>";
        }

    }else{
        echo "查無此會員";
    }
       
?>