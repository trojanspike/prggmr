<?php

// INVALID_HANDLE
prggmr\handle('a', 'b');

// HANDLE_EXCEPTION
prggmr\handle(function(){
    throw new Exception('test');
}, 'error');

prggmr\signal('error');

// INVALID_SIGNAL
prggmr\signal(array());

// INVALID_EVENT
prggmr\signal(array(), null, $str = 'asd');

// INVALID_HANDLE_DIRECTORY
prggmr\handle_loader('a', '/asjhfasf');