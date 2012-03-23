<?php

require '../src/prggmr.php';
require '../src/signal/array_contains_signal.php';
$engine = new \prggmr\Engine();
$engine->handle(function(){}, 'b');
$engine->handle(function(){}, 'a');
$engine->handle(function(){}, 'a');
$engine->handle(function(){}, new \prggmr\signal\ArrayContains(array(
    'test'
)));