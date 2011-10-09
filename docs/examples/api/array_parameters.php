<?php
// Subcribe with multi params
subscribe(function($event, $array){
    var_dump($array);
}, 'Arrays', "ArrayPassing");

// This fails!
fire('Arrays', array("Hello", "World"));

echo PHP_EOL;

// This works!
fire('Arrays', array(array("Hello", "World")));

// shutdown prggmr
shutdown();