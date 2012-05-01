<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use prggmr\signal\unit_test\api as test;

test\suite(function(){

    $this->setup(function(){
        // setup function
    });

    $this->teardown(function(){
        // teardown function
    })

    $this->test(function(){
        $this->true(true);
    }, 'Queue Test');

});