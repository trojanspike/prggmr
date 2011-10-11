<?php
setTimeout(function(){
    echo "Half A second just passed!".PHP_EOL;
}, 500, null, 'myTimeout'); 

setTimeout(function(){
    clearTimeout('myTimeout2');
}, 999, null, 'clearer');

setTimeout(function(){
    echo "A second just passed!".PHP_EOL;
}, 1000, null, 'myTimeout2');