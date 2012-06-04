<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
prggmr\load_signal('unittest');

use prggmr\signal\unittest as unittest;

unittest\api\suite(function(){

    $this->setup(function(){
        $this->engine = new prggmr\Engine();
    });

    $this->teardown(function(){
        unset($this->engine);
    });

    $this->test(function(){

    });
    
});