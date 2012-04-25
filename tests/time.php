<?php

// prggmr\interval(function(){
//     echo "1 second passed";
// }, 1000);

// prggmr\timeout(function(){
//     echo "Shutting down the engine";
// }, 1000);
// // 
// prggmr\interval(function(){
//     $queue = prggmr\signal_queue('helloworld');
//     switch($queue[0]) {
//         case \prggmr\Engine::QUEUE_NEW:
//             echo "A new queue for the helloworld signal has been created".PHP_EOL;
//             break;
//         case \prggmr\Engine::QUEUE_EMPTY:
//             echo "Located the helloworld signal queue but it is empty.".PHP_EOL;
//             prggmr\handle(function(){
//                 echo "Helloworld".PHP_EOL;
//             }, "helloworld", null, null);
//             break;
//         case \prggmr\Engine::QUEUE_NONEMPTY:
//             echo "Located the helloworld signal queue and it is not empty.".PHP_EOL;
//             echo "Signaling helloworld".PHP_EOL;
//             prggmr\signal("helloworld");
//             break;
//     }
// }, 1000);

prggmr\handle(function(){
    $this->a = 1;
}, "test", 0);

prggmr\handle(function(){
    var_dump($this->a);
}, "test", 1);

prggmr\handle(function(){
    var_dump(isset($this->a));
}, "test2");

$event = new prggmr\Event();
prggmr\signal('test', null, $event);
prggmr\signal('test2', null, $event);

// prggmr\signal('test');
// prggmr\signal('test2');