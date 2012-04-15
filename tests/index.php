<?php

require '../src/prggmr.php';
require '../src/signal/http/request.php';

prggmr\signal\http\handle_request(function(){
    echo "Hello World";
}, "/");

prggmr\prggmr_loop();