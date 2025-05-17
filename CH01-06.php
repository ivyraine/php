<?php

echo "if-else練習 <br><br>";

$score = 58;

if($score >= 60){
    echo "及格";
}else{
    echo "不及格";
}
echo"<br><br>";

$score = 90;
echo $score>=60 ? "及格" :"不及格";

echo"<br><br><hr>";


echo "if-else練習2 <br><br>";

$score=99;

if($score >=90){
    if($score >=95){
        echo "A+";
    }else{
        echo "A";
    }
}else if($score >= 80){
    echo "B";
}else if($score >= 70){
    echo "C";
}else if($score >60){
    echo "D";
}else {
    "不及格";
}
echo"<br><br><hr>";


echo "switch練習<br><br>";
$type = 2;

switch($type){
    case "1":
        echo "6000";
        break;

    case "2":
        echo "4000";
        break;
    default:
        echo "0";
}
echo"<br><br><hr>";


echo "for練習<br><br>";

for($i=0; $i<10; $i++){
    echo $i;
}

echo"<br><br><hr>";


echo "課堂練習: 利用for loop計算1+2+3+….+50總和 <br><br>";

$sum = 0;

for($i=1; $i<=50; $i++){
    $sum += $i;
}
    echo "總和: $sum";
echo"<br><br><hr>";


echo "課堂練習2: 印出 1~50之間 3的倍數，並計算這些數的總和 <br><br>";

$sum2 = 0;

for($j=1;$j<=50;$j++){
    if($j % 3 == 0){
        // echo "$j ";
        $sum2+=$j;
    }
}
    echo "總和: $sum2";

echo"<br><br><hr>";


echo "while練習<br><br>";

$i=0; 
while($i<10){
    echo $i;
    $i++;
}
echo"<br><br><hr>";


echo "課堂練習: 利用While loop計算1+2+3+….+50總和<br><br>";

$sum = 0;
$i=1;
while($i<=50){
    $sum += $i;
    $i++;
}
    echo "總和: $sum";
echo"<br><br><hr>";

echo "課堂練習2: 印出 1~50之間 3的倍數，並計算這些數的總和<br><br>";

$sum2 = 0;
$j=1;
while($j<=50){
    if($j % 3 == 0){
        // echo "$j ";
        $sum2+=$j;
    }
    $j++;
}
    echo "總和: $sum2";
echo"<br><br><hr>";


echo "do-while練習<br><br>";

$i=10; 
while($i<10){
    echo $i;
    $i++;
}

$i=10;
do{
    echo $i;
    $i++;
}while($i<10);

echo"<br><br><hr>";


echo "練習: 九九乘法表<br><br>";
for($i=1;$i<=9;$i++){
    for($j=1;$j<=9;$j++){

        echo $i."*".$j."=".$i*$j;
        echo "<br>";
    }
    echo "<br>";
}
?>