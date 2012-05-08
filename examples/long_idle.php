<?php
prggmr\handle(function(){
    echo "Loop Start";
}, \prggmr\engine\Signals::LOOP_START);

prggmr\handle(function(){
    echo "Loop END";
    var_dump(prggmr\prggmr());
}, \prggmr\engine\Signals::LOOP_SHUTDOWN);

prggmr\interval(function(){
    echo "1 1/2 Second".PHP_EOL;
}, 999);

prggmr\timeout(function(){
    prggmr\prggmr_shutdown();
    echo "5 Seconds".PHP_EOL;
}, 5000);