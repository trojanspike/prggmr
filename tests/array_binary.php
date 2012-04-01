<?php
require_once '../src/signal/array_binary.php';
require_once '../src/signal/range.php';

handle(function($value){
    echo "This used a binary array search";
}, new \prggmr\signal\ArrayBinary(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));

signal(1);

// $array = [0, 1, 2, 3];
// var_dump($array);
// var_dump(isset($array["0"]));
// var_dump(array_key_exists("0", $array));