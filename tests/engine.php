<?php
require '../src/prggmr.php';
require '../src/signal/array_contains.php';

handle(function(){
    echo "RESTRICTED HANDLE REGISTERED";
}, \prggmr\engine\Signals::RESTRICTED_SIGNAL);

handle(function($exception, $signal){
    echo "Hello Exception";
}, \prggmr\engine\Signals::HANDLE_EXCEPTION);

handle(function(){
    throw new \Exception(0);
    $this->test = "Sweet!";
}, 'test', "id", 0);

handle(function(){
    echo $this->test;
}, 'test', "id", 1);

signal('test');