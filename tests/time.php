<?php

require '../src/prggmr.php';

$interval = interval(function(){
    echo "1 second passed";
}, 1000);

var_dump($interval);

timeout(function(){
    echo "Shutting down the engine";
    prggmr_shutdown();
}, 5000);

prggmr_loop();