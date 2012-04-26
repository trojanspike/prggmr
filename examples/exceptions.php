<?php

require '../src/signal/query.php';
// INVALID_HANDLE
//handle('a', 'b');

// HANDLE_EXCEPTION
// handle(function(){
//     throw new Exception('test');
// }, 'error');
// signal('error');

// INVALID_SIGNAL
//signal(array());

// INVALID_EVENT
//signal(array(), null, $str = 'asd');

// INVALID_HANDLE_DIRECTORY
//handle_loader('a', '/asjhfasf');

handle(function(){
        echo "HelloWorld_1";
    }, "helloworld");

    handle(function(){
        echo "HelloWorld_2";
    }, "helloworld", null, null);

    while(true) {
        signal('helloworld');
    }