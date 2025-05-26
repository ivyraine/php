<?php

       include("conn.php");

       //---------------------------------------------------

       $account = $_POST["account"];
       $password = $_POST["password"];

       //建立SQL語法
        //$sql = "SELECT * FROM member WHERE account = :account and PWD = :pwd";

       //執行並查詢，會回傳查詢結果的物件，必須使用fetch、fetchAll...等方式取得資料
        //$statement = $pdo->query($sql); //這寫法會有SQL Injection
        //bindParam() - 自定義名稱表示
        // $statement = $pdo->prepare($sql); 
        // $statement->bindParam(":account", $account);
        // $statement->bindParam(":pwd", $password);
        // $statement->execute();
        
        //bindParam() - 問號提示
        // $sql = "SELECT * FROM member WHERE account = ? and PWD = ?";
        // $statement = $pdo->prepare($sql); 
        // $statement->bindParam(1, $account);
        // $statement->bindParam(2, $password);
        // $statement->execute();

        //bindValue() - 自定義名稱表示
        // $sql = "SELECT * FROM member WHERE account = :account and PWD = :pwd";
        // $statement = $pdo->prepare($sql); 
        // $statement->bindValue(":account", $account);
        // $statement->bindValue(":pwd", $password);
        // $statement->execute();

        //bindValue() - 問號表示
        $sql = "SELECT * FROM member WHERE account = ? and PWD = ?";

        $statement = $pdo->prepare($sql); 
        $statement->bindValue(1, $account);
        $statement->bindValue(2, $password);
        $statement->execute();

       //抓出全部且依照順序封裝成一個二維陣列
       $data = $statement->fetchAll();

       if(count($data) > 0){
          //   echo "登入成功";
          session_start();
          $_SESSION["memberID"] = $account;
          //redirect 導向(踢去) welcome頁面
          header("Location: Welcome.php");

       }else{
            echo "帳號或密碼錯誤";
       }
       
       //將二維陣列取出顯示其值
    //    foreach($data as $index => $row){
	//        echo $row["Account"];   //欄位名稱
	//        echo " / ";
	//        echo $row["PWD"];    //欄位名稱
	//        echo " / ";
	//        echo $row["CreateDate"];    //欄位名稱
    //           echo "<br>";
    //    }

?>