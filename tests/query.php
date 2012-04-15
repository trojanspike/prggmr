<?php
require_once '../src/signal/string/query.php';

handle(function($name){
    echo "Hello $name";
}, new \prggmr\signal\string\Query('user/:name'));

signal('/user/prggmr');

// $array = [0, 1, 2, 3];
// var_dump($array);
// var_dump(isset($array["0"]));
// var_dump(array_key_exists("0", $array));