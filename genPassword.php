<?php

$eng = range('a', 'z');
$num = range(0, 9);
$password = array_merge($eng, $num);

shuffle($password);

for($i=0; $i < 10; $i++){
    echo $password[$i];
}

?>