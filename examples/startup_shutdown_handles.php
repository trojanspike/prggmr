<?php

prggmr\handle(function(){
    echo "The engine is shutting down!";
}, \prggmr\engine\Signals::LOOP_SHUTDOWN);

prggmr\handle(function(){
    echo "The loop is starting";
}, \prggmr\engine\Signals::LOOP_START);

prggmr\timeout(function(){
    echo "Delaying a second";
}, 1000);