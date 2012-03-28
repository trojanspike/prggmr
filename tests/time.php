<?php

require '../src/prggmr.php';

// $interval = interval(function(){
//     echo "1 second passed";
// }, 1000);

// timeout(function(){
//     echo "Shutting down the engine";
//     prggmr_shutdown();
// }, 5000);
// 
interval(function(){
    $queue = signal_queue('helloworld');
    switch($queue[0]) {
        case \prggmr\Engine::QUEUE_NEW:
            echo "A new queue for the helloworld signal has been created".PHP_EOL;
            break;
        case \prggmr\Engine::QUEUE_EMPTY:
            echo "Located the helloworld signal queue but it is empty.".PHP_EOL;
            handle(function(){
                echo "Helloworld".PHP_EOL;
            }, "helloworld", null, null);
            break;
        case \prggmr\Engine::QUEUE_NONEMPTY:
            echo "Located the helloworld signal queue and it is not empty.".PHP_EOL;
            echo "Signaling helloworld".PHP_EOL;
            signal("helloworld");
            break;
    }
}, 1000);

prggmr_loop();