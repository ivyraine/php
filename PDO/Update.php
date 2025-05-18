<?php
       include("conn.php");
       
       //---------------------------------------------------

       //建立SQL
       $sql = "UPDATE member SET PWD = 'Tibame123' WHERE Account = '王小明'";

       //執行
       $affectedRow = $pdo->exec($sql);
       echo $affectedRow;
       // if($affectedRow > 0){
       //        echo "更新成功!";
       // }else{
       //        echo "更新失敗!";
       // }

?>