<?php

/**
 *  Test the number of operations performed per second.
 */
class OPS extends prggmr\signal\Complex 
{
    public function routine($h = null) 
    {
        return [null, ENGINE_ROUTINE_SIGNAL, 0];
    }
}

$a = 0;
// Handle an unlimited amount of Ops
prggmr\handle(function() use (&$a) {
    $a++;
}, new Ops(), null, null);

// Run the test for 1 Second
prggmr\timeout(function(){
    prggmr\shutdown();
}, 10000);

prggmr\handle(function() use (&$a){
    echo "Ran $a Complex signal calcuations".PHP_EOL;
}, prggmr\engine\Signals::LOOP_SHUTDOWN);