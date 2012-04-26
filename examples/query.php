<?php
prggmr\load_signal('string');

prggmr\handle(function($name){
    echo "Hello $name";
}, new \prggmr\signal\string\Query('user/:name'));

prggmr\signal('/user/prggmr');