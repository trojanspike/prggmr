<?php
subscribe(function($event){
    echo "Hello";
}, "Priority", "Second", 2);

subscribe(function($event){
    echo "World";
}, "Priority", "First", 1);

fire("Priority");

// shutdown prggmr
shutdown();