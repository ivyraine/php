<?php
// 方法一
// echo "A身高: ".$_GET["A"]."公分";
// echo "<br>";
// echo "B身高: ".$_GET["B"]."公分";
// echo "<br>";
// echo "C身高: ".$_GET["C"]."公分";
// echo "<br><br>";

// $arr = [$_GET["A"], $_GET["B"], $_GET["C"]];

// echo "身高最高: ".max($arr)."公分";
// echo "<br>";
// echo "平均身高: ".array_sum($arr) / count($arr)."公分";


// 方法二
// $arr = $_GET["height"];
echo max($_GET["a"]);
echo "<br>";
echo array_sum($_GET["a"]) / count($_GET["a"]);

?>