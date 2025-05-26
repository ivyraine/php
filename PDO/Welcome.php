<?php

// <script> 未完 問同學 
//     window.addEventListener('pageshow', function(event){
//         if(event.persisted{
//             //頁面是從快取來的 重新載入
//             window.location.reload();
//         })
//     })
// </script>

//login check
// 判斷有登入才可以看這頁 拿 key 跟session 要資料
session_start();

if(isset($_SESSION["memberID"])){
    echo "Hi~".$_SESSION["memberID"];
}else{
    echo "<script>
            alert('請先登入!!');
            location.href='Login.html';
        </script>";
    // header("Location: login.html"); //沒登入 踢去login頁面 //改透過JS轉址
}

?>

<br>
<a href="Logout.php">登出</a>