<?php

       include("conn.php");

       //---------------------------------------------------

       //建立SQL語法
       $sql = "SELECT * FROM member";

       //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
       $statement = $pdo->query($sql);

       //fetch() 回傳一筆資料，為一維陣列 建議直接在mysql處理 所以很少用!
       // $data = $statement->fetch();
       // print_r($data);
       //回傳結果為: 同一筆資料顯示關聯 (欄位當成key) 與索引陣列
       // Array ( 
       //        [Account] => 王小明 
       //        [PWD] => Tibame123 
       //        [CreateDate] => 2025-05-17 14:55:25 

       //        [0] => 王小明 
       //        [1] => Tibame123 
       //        [2] => 2025-05-17 14:55:25 
       // )

       //fetchColumn() 回傳一筆資料，為一維陣列 建議直接在mysql處理 所以很少用!
       // $data = $statement->fetchColumn(1);
       // print_r($data);

       //抓出全部且依照順序封裝成一個二維陣列
       $data = $statement->fetchAll();
       
       // print_r($data);

       //回傳結果為: 同一筆資料顯示關聯 (欄位當成key) 與索引陣列
       // Array ( 
       //        [0] => Array ( 
       //               [Account] => 王小明 
       //               [PWD] => Tibame123 
       //               [CreateDate] => 2025-05-17 14:55:25 
       //               [0] => 王小明 
       //               [1] => Tibame123 
       //               [2] => 2025-05-17 14:55:25 
       //        ) 
       //        [1] => Array ( 
       //               [Account] => 王小明 
       //               [PWD] => Tibame123 
       //               [CreateDate] => 2025-05-17 15:02:36 
       //               [0] => 王小明 
       //               [1] => Tibame123 
       //               [2] => 2025-05-17 15:02:36 
       //        ) 
       // )

       //將二維陣列取出顯示其值
       foreach($data as $index => $row){
	       echo $row["Account"];   //欄位名稱
	       echo " / ";
	       echo $row["PWD"];    //欄位名稱
	       echo " / ";
	       echo $row["CreateDate"];    //欄位名稱
              echo "<br>";
       }

?>