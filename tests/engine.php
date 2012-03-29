<?php

handle(function(){
    echo "The engine is shutting down!";
}, \prggmr\engine\Signals::LOOP_SHUTDOWN);

handle(function(){
    echo "The loop is starting";
}, \prggmr\engine\Signals::LOOP_START);

timeout(function(){
    echo "1 second just passed";
}, 1000);