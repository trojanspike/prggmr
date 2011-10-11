<?php
$myinterval = setInterval(function($event){
    echo "Half A second just passed!".PHP_EOL;
}, 500); 

setInterval(function($event){
    echo "A second just passed!".PHP_EOL;
}, 1000);

// Lets clear the interval after 5 seconds
setTimeout(function($event) use ($myinterval){
    echo "Clearing the Interval".PHP_EOL;
    clearInterval($myinterval);
}, 3000);