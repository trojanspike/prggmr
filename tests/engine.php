<?php
require '../src/prggmr.php';

handle(function(){
    throw new \Exception(0);
    $this->test = "Sweet!";
}, 'test', "id", 0);

handle(function(){
    echo $this->test;
}, 'test', "id", 1);

signal('test');