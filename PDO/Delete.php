<?php

       include("conn.php");

       //---------------------------------------------------

       //建立SQL
       $sql = "DELETE FROM member WHERE Account = '王小明'";

       //執行
       $affectedRow = $pdo->exec($sql);
       if($affectedRow > 0){
              echo "刪除成功!";
       }else{
              echo "刪除失敗!";
       }

?>