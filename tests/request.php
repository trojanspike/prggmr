<?php

define('BASE_URI', '/request.php');

require '../src/prggmr.php';
require '../src/signal/http/request.php';

prggmr\signal\http\handle_request(function($id, $post){
    echo "This is a post $id $post";
}, "/user/:id/:post_number") ;

prggmr_loop();