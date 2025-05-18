<?php
       include("conn.php");

       //---------------------------------------------------

       //建立SQL
       $sql = "INSERT INTO member(Account, PWD, CreateDate) VALUES ('王小明', 'abc123', NOW())";

       //執行
       $affectedRow = $pdo->exec($sql);
       if($affectedRow > 0){
              echo "新增成功!";
       }else{
              echo "新增失敗!";
       }

?>