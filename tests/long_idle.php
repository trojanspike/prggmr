<?php
handle(function(){
    echo "Loop Start";
}, \prggmr\engine\Signals::LOOP_START);

handle(function(){
    echo "Loop END";
    var_dump(prggmr::instance());
}, \prggmr\engine\Signals::LOOP_SHUTDOWN);

interval(function(){
    echo "1 1/2 Second".PHP_EOL;
}, 999);

timeout(function(){
    prggmr_shutdown();
    echo "5 Seconds".PHP_EOL;
}, 5000);