<?php
require '../src/prggmr.php';

handle(function(){
    throw new \Exception('test');
}, 'test-child');

handle(function(){
    echo "HELLo";
}, 'test-child', 0);

signal('test-child');
// handle(function(){
//     $this->test = "ROOT EVENT";
// }, 'test', 0);

// handle(function(){
//     echo $this->test.PHP_EOL;
//     signal('test-child');
// }, 'test', 1);

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
