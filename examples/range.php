<?php
prggmr\load_signal('integer');

prggmr\handle(function($name){
    echo "Hello $name";
}, new prggmr\signal\integer\Range(0, 100));

prggmr\signal(57);

// $array = [0, 1, 2, 3];
// var_dump($array);
// var_dump(isset($array["0"]));
// var_dump(array_key_exists("0", $array));