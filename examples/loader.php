<?php
require_once '../src/signal/string/query.php';
prggmr\handle_loader(
    new prggmr\signal\string\Query("test_:name"), 
    "/Users/prggmr/Work/prggmrlabs/prggmr/tests/loader"
);
prggmr\signal("test_hey");
var_dump(prggmr::instance());