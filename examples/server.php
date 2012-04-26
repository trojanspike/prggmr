<?php
/**
 * Non-Blocking Server Example
 */
handle(function(){
    $this->headers(['Content-Type': 'text/plain']);
    $this->write('Hello World');
    return 200;
}, \prggmr\signal\sockets\HttpServer('127.0.0.1', 1337));

print "Server is running at 127.0.0.1:1337";