<?php
// Subscribe
subscribe(function($event){
    echo "HelloWorld";
}, "HelloWorld", "Hello World Example");

// Fire
fire("HelloWorld");

// shutdown prggmr
shutdown();