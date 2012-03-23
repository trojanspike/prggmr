<?php

handle(function(){
    echo "A wedding just happened!";
}, new \prggmr\signal\History(array(
    'wedding_bells',
    'man_tuxedo',
    'woman_white_dress'
)));

signal('wedding_bells');
signal('man_tudexo');
signal('woman_white_dress');