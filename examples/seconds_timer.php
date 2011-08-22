<?php
set_include_path(get_include_path().DIRECTORY_SEPARATOR.dirname(realpath(__FILE__)).'/../');
require 'ilb/prggmr.php';
/**
 * A timer that counts and displays the number of seconds it has run
 * every second.
 */
$count = 0;
setInterval(function($event, $count){
    echo sprintf(
        "Running for %d seconds now ... \n",
        $count++
    );
}, 1000, array(&$count));
