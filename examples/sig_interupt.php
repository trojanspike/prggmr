<?php

prggmr\signal_interrupt(function(){
    $this->a = "I interrupted this call!";
}, "test");

prggmr\handle(function(){
    echo $this->a;
}, "test");

prggmr\signal("test");

prggmr\signal_interrupt(function(){
    echo "I'm stopping the stack right here";
    $this->halt();
}, "helloworld");

prggmr\handle(function(){
    echo "hellWorld";
}, "helloworld");

prggmr\signal("helloworld");