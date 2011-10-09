<?php
subscribe(function($event){
    echo "Hello";
}, 'HelloWorld', 'Hello');

subscribe(function($event){
    echo "World";
}, 'HelloWorld', 'World');

// remove the Hello subscriber
dequeue('HelloWorld', 'Hello');

fire("HelloWorld");
// shutdown prggmr
shutdown();