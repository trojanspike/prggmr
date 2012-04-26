<?php
prggmr\load_signal('arrays');

prggmr\handle(function($value){
    echo "This used a array search";
    echo $value;
}, new \prggmr\signal\arrays\Contains(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));

prggmr\signal(1);