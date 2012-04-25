<?php
require '../../src/signal/unit_test/__autoload.php';

prggmr\signal\unit_test\api\test(function(){
    $this->true(true);
    $this->true(false);
    $this->true(true);
    $this->true(true);
    $this->true(true);
    $this->true(true);
});

prggmr\signal\unit_test\api\test(function(){
    $this->object(new \stdClass());
});