<?php
require '../src/prggmr.php';
require '../src/signal/array_contains_signal.php';

handle(function($exception, $signal){
    var_dump($exception->getTraceAsString());
}, \prggmr\engine\Signals::HANDLE_EXCEPTION);

handle(function(){
    $this->test = "Sweet!";
    signal('test-2', null, $this);
}, 'test', "id", 0);

handle(function(){
    signal('test-3');
}, 'test-2', "id", 1);

handle(function(){
    throw new \Exception('Something bad happened');
}, 'test-3');

signal('test');