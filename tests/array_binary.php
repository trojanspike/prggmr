<?php
prggmr\load_signal('array');

prggmr\handle(function($value){
    echo "This used a binary array search";
}, new \prggmr\signal\array\Binary(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));

prggmr\signal(1);

// $array = [0, 1, 2, 3];
// var_dump($array);
// var_dump(isset($array["0"]));
// var_dump(array_key_exists("0", $array));
