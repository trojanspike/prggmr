<?php

define('BINARY_ENGINE_SEARCH', 0);

require '../src/prggmr.php';
$engine = new \prggmr\Engine();
$engine->handle(function(){}, 'b');
$engine->handle(function(){}, 'a');
$engine->handle(function(){}, 'a');
var_dump($engine);
