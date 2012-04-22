<?php

define('BASE_URI', '/request.php');

require '../src/signal/http/url.php';

prggmr\handle(function($id){
    echo "View user $id";
}, new prggmr\signal\http\Url("/user/:id"), 'GET');

prggmr\handle(function($id){
    echo "View user $id";
}, new prggmr\signal\http\Url("/user/:id"), 'GET');

prggmr\interval(function(){
    if (!isset($this->a)) {
        $this->a = 1;
    } else {
        $this->a++;
    }
    echo $this->a;
}, 1000, null, null, 5);

prggmr\timeout(function(){
    echo "a";
}, 10);

prggmr\prggmr_loop();