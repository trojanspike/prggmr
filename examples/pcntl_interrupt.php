<?php
// you must declare ticks for this signal
declare(ticks=1);
prggmr\load_signal('pcntl');
prggmr\handle(function(){
    echo PHP_EOL."CATCHING SHUTDOWN".PHP_EOL;
    // if you dont exit it will not exit on SIGINT
    exit;
}, new prggmr\signal\pcntl\Interrupt());

// $engine = new prggmr\Engine();
// $engine->handle(function(){
//     echo PHP_EOL."CATCHING SHUTDOWN".PHP_EOL;
//     exit
// }, new prggmr\signal\pcntl\Interrupt($engine));
// inifite loop to demonstrate ctrl-c to exit
while(1){}