<?php
    //判斷是否上傳成功
    if($_FILES["profile"]["error"] > 0){
        echo "上傳失敗: 錯誤代碼".$_FILES["profile"]["error"];
    }else{
        //取得上傳的檔案資訊=======================================
        $fileName = $_FILES["profile"]["name"];    //檔案名稱含副檔名        
        $filePath_Temp = $_FILES["profile"]["tmp_name"];   //Server上的暫存檔路徑含檔名        
        $fileType = $_FILES["profile"]["type"];    //檔案種類        
        $fileSize = $_FILES["profile"]["size"];    //檔案尺寸
        //=======================================================

        //Web根目錄真實路徑
        $ServerRoot = $_SERVER["DOCUMENT_ROOT"];
        
        //檔案最終存放位置
        $filePath = $ServerRoot."/FileUpload/".$fileName;
  
        //將暫存檔搬移到正確位置
        move_uploaded_file($filePath_Temp, $filePath);

        //顯示檔案資訊
        echo "檔案存放位置：".$filePath;
        echo "<br/>";
        echo "類型：".$fileType;
        echo "<br/>";
        echo "大小：".$fileSize;
        echo "<br/>";
        echo "副檔名：".getExtensionName($filePath);
        echo "<br/>";
        echo "<img src='/FileUpload/".$fileName."'/>";
    }

    //取得檔案副檔名
    function getExtensionName($filePath){
        $path_parts = pathinfo($filePath);
        return $path_parts["extension"];
    }
?>
