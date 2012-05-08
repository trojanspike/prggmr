<?php

// This demonstrates the ability to run two or more engines in the same script
$engine1 = new prggmr\Engine();
$engine2 = new prggmr\Engine();

$engine1->handle(function(){
    echo "This is engine #1".PHP_EOL;
}, 'test');

$engine2->handle(function(){
    echo "This is engine #2".PHP_EOL;
}, 'test');

echo "Signal engine #1".PHP_EOL;
$engine1->signal('test');
echo "Signal engine #2".PHP_EOL;
$engine2->signal('test');

var_dump($engine1);
var_dump($engine2);