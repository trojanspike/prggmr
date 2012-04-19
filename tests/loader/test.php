<?php
require_once '../src/signal/string/query.php';
prggmr\handle(function($name){
    var_dump(func_get_args());
    echo $name;
}, new prggmr\signal\string\Query('test_:name'));
