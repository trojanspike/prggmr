<?php
require '../src/prggmr.php';

prggmr\handle(function() {
    echo 'hey';
}, 'test');

prggmr\signal('test');