<?php

prggmr::instance()->signal_interrupt(function(){
    $this->a = "HAHAHA";
    return false;
}, "test");

prggmr\handle(function(){
    echo $this->a;
}, "test");

prggmr\signal("test");