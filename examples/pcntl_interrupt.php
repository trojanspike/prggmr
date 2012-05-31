<?php

prggmr\load_signal('pcntl');

prggmr\handle(function(){
    echo "CATCHING SHUTDOWN";
    exit;
}, new prggmr\signal\pcntl\Interrupt());

// inifite loop to demonstrate ctrl-c to exit
while(1){}