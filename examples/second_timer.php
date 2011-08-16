<?php
require '../lib/prggmr.php';

$count = 0;
setInterval(function($event, $count){
    echo sprintf(
        "Running for %d seconds now ... \n",
        $count++
    );
}, 1000, array(&$count));


// start it all
prggmrd();
?>