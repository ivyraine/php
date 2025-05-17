<?php

echo"for迴圈<br><br>";
$arr = [80, 93, 45];

for($i=0; $i < count($arr); $i++){
    echo $arr[$i];
    echo " ";
}
echo"<br><br><hr>";


echo"陣列 foreach<br><br>";
$arr =[80, 93, 45];

foreach($arr as $key => $value){
    echo $value;
}

foreach($arr as $key => $value){
    echo $key.': '.$value;
    echo "<br>";
}
echo"<br><br><hr>";

echo"關聯陣列寫法<br><br>";
$arr = ["甲"=>80, "乙"=>93, "丙"=>45];

foreach($arr as $a => $b){
    echo $a.': '.$b;
    echo "<br>";
}
echo"<br><br><hr>";


echo"print 用法<br><br>";
$arr = ["甲"=>80, "乙"=>93, "丙"=>45];

print_r($arr);
echo"<br><br><hr>";


echo"push 用法<br><br>";
$arr = [80, 93, 45];

$arr = ["甲"=>80, "乙"=>93, "丙"=>45];
array_push($arr, 99);
print_r($arr);
echo"<br><br><hr>";


echo"merge 用法<br><br>";
$arr = ["甲"=>80, "乙"=>93, "丙"=>45];
$arr2 = ["丁"=>99];
$arr3 = array_merge($arr, $arr2);
print_r($arr3);
echo"<br><br><hr>";


echo "search 用法";
echo "<br><br>";
$arr = ["甲"=>80, "乙"=>93, "丙"=>45, "丁"=>100];
echo array_search(100, $arr);
echo "<br><br><hr>";


echo "shuffle 用法";
echo "<br><br>";

$arr = ["甲"=>80, "乙"=>93, "丙"=>45];
shuffle($arr);
print_r($arr);
echo "<br><br><hr>";


echo "加總用法";
echo "<br><br>";

$arr = ["甲"=>80, "乙"=>93, "丙"=>45];
$total = array_sum($arr);
echo "總分: $total";

echo "<br><br><hr>";


echo "課堂練習: 請參考以下步驟將最大值為50的5的倍數封裝到一個陣列內，重新排序，最後透過print_r()顯示結果";
echo "<br><br>";


$arr = [];

for($i=1; $i<=50; $i++){
    if($i % 5 == 0){
        array_push($arr, $i);
    }
}
shuffle($arr);
print_r($arr);

echo "<br><br><hr>";


echo "二維陣列 foreach";
echo "<br><br>";
$arr = [ [80, 75], [93, 55], [45, 92] ];

foreach($arr as $key => $sub_array){
    foreach($sub_array as $sub_key => $value){
        echo $value;
        echo "<br>";
    }
}
echo "<br><hr>";

echo "課堂練習";
echo "請試著算出: 英文最高分 數學最低分 英文平均分數 數學平均分數?<br><br>";

$arr = [ 
    "A同學" => [ "英文" => 80, "數學" => 75], 
    "B同學" => [ "英文" => 93, "數學" => 55], 
    "C同學" => [ "英文" => 45, "數學" => 92] 
];

$engScore = [];
$mathScore = [];

foreach($arr as $key => $subArr){
    $engScore[] = $subArr["英文"];
    $mathScore[] = $subArr["數學"];
}

echo "英文最高分".max($engScore);
echo "<br>";

echo "數學最低分".min($mathScore);
echo "<br>";

echo "英文平均分數".array_sum($engScore) / count($engScore);
echo "<br>";

echo "數學平均分數".array_sum($mathScore) / count($mathScore);
echo "<br><br>";


echo"老師寫法<br>";
$arr_en = [];
$arr_ma = [];

$arr = [ 
"A同學" => [ "英文" => 80, "數學" => 75], 
 "B同學" => [ "英文" => 93, "數學" => 55], 
                "C同學" => [ "英文" => 45, "數學" => 92] 
];

foreach ($arr as $who => $grade){
    foreach ($grade as $subject => $score){
        switch ($subject) {
            case "英文":    //組成英文分數陣列
                array_push($arr_en, $score);
                break;
            case "數學":    //組成數學分數陣列
                array_push($arr_ma, $score);
                break;
            default:
                // code...
                break;
        }
    }
}

echo "英文最高分".max($arr_en);
echo "<br/>";
echo "數學最低分".min($arr_ma);
echo "<br/>";
echo "英文平均分數".array_sum($arr_en) / count($arr_en);
echo "<br/>";
echo "數學平均分數".array_sum($arr_ma) / count($arr_ma);
echo"<br><br><hr>";


echo "作業1: 隨機印出10碼亂數密碼，包含英文和數字<br><br>";
$eng = range('a', 'z');
$num = range(0, 9);
$password = array_merge($eng, $num);

shuffle($password);

for($i=0; $i < 10; $i++){
    echo $password[$i];
}

echo"<br><br><hr>";

?>