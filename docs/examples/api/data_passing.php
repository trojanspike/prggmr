<?php
subscribe(function($event){
    $event->hello = "HelloWorld";
}, "PassData", "SetVar");

subscribe(function($event){
    echo $event->hello;
}, "PassData", "EchoVar");

fire("PassData");

// shutdown prggmr
shutdown();