<?php
require '../src/prggmr.php';
/**
 * This must run directly in your browser!
 * 
 * Use the php built server:
 * php -S 127.0.0.1:5000 index.php
 */
prggmr\load_signal('http');

use prggmr\signal\http\api as http;

http\uri_request(function(){
    echo "Hello World";
}, "/");

http\uri_request(function($name){
    echo "Hello $name";
}, "/user/:name");

prggmr\prggmr_loop();