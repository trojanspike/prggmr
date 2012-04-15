<?php

define('BASE_URI', '/request.php');

require '../src/prggmr.php';
require '../src/signal/http/url.php';

prggmr\signal\http\handle_url(function($id, $post){
    echo "This is a post $id $post";
}, "/user/:id/:post_number") ;

prggmr\prggmr_loop();