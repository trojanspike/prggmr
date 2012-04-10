<?php
interval(function(){
    echo exec('tput cols');
    echo PHP_EOL;
    echo exec('tput lines');
}, 1000);