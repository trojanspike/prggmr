<?php
require '../src/prggmr.php';
require '../src/signal/array_contains_signal.php';

handle(function(){
    echo "IM HERE";
    $this->test = "Sweet!";
}, 'test', "id", 0);

handle(function(){
    echo "IM FIRING";
    echo $this->test;
}, 'test', "id", 1);

signal('test');