<?php

prggmr\handle(function(){
    var_dump($this);
    var_dump($this->set_signal([]));
    var_dump($this);
}, "test");

prggmr\signal("test");