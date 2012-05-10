<?php

prggmr\handle(function(){
    prggmr\signal('a');
}, 'b');

prggmr\handle(function(){
    prggmr\signal('c');
}, 'a');

prggmr\signal('b');
prggmr\signal('c');

// !! LIES !! no b never existed or a or c!
prggmr\clean(true);

var_dump(prggmr\prggmr());