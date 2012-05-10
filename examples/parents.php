<?php

prggmr\handle(function(){
    prggmr\signal('a');
}, 'b');

prggmr\handle(function(){
    prggmr\signal('c');
}, 'a');

prggmr\signal('b');

var_dump(prggmr\event_history());