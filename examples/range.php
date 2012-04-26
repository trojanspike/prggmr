<?php
require_once '../src/signal/integer/range.php';

handle(function($name){
    echo "Hello $name";
}, new \prggmr\signal\integer\Range(0, 100));

signal(57);

// $array = [0, 1, 2, 3];
// var_dump($array);
// var_dump(isset($array["0"]));
// var_dump(array_key_exists("0", $array));