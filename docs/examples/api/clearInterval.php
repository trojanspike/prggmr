<?php
setInterval(function($event){
    echo "Half A second just passed!".PHP_EOL;
}, 500, null, 'myInterval'); 

setInterval(function($event){
	echo "A second just passed!".PHP_EOL;
}, 1000, null, 'myInterval2');

// Lets clear the interval after 5 seconds
setTimeout(function($event){
    echo "Clearing the Interval".PHP_EOL;
    clearInterval('myInterval');
}, 3000);