<?php
require '../src/prggmr.php';

setTimeout(function(){
    echo "10 seconds just passed";
}, 10000);

prggmr_loop();

// handle(function(){
//     echo "I'm a root child event".PHP_EOL;
//     signal('test-child-child');
// }, 'test-child');

// handle(function(){
//     echo "I'm a second level child".PHP_EOL;
// }, 'test-child-child');

// handle(function(){
//     echo "I'm another child of root with a lower priority".PHP_EOL;
// }, 'test-child', 101);

// handle(function(){
//     echo "i throw an exception";
//     throw new \Exception('testing exceptions');
// }, 'test');
