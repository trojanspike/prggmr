<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
prggmr\load_signal('unittest');

use prggmr\signal\unittest as unittest;

unittest\api\suite(function(){

    $this->test(function(){
        $this->exception('InvalidArgumentException', function(){
            $handle = new prggmr\Handle(null);
        });
        $handle = new prggmr\Handle(function(){}, 'a');
        $this->equal($handle->exhaustion(), 1);
        $handle = new prggmr\Handle(function(){}, -1);
        $this->equal($handle->exhaustion(), 1);
    }, "handle construction");

    $this->test(function(){
        $a = null;
        $handle = new prggmr\Handle(function() use (&$a){
            $a = $this;
        });
        $handle();
        $this->instanceof('stdClass', $a);
    }, "Handle binding");

    $this->test(function(){
        $handle = new prggmr\Handle(function(){});
        $this->false($handle->is_exhausted());
        $handle();
        $this->true($handle->is_exhausted());
        $handle = new prggmr\Handle(function(){}, 2);
        $handle();
        $this->false($handle->is_exhausted());
        $handle();
        $this->true($handle->is_exhausted());
        $handle = new prggmr\Handle(function(){}, null);
        for ($i=0;$i!=5;$i++) { $handle(); }
        $this->false($handle->is_exhausted());
        $handle = new prggmr\Handle(function(){}, 0);
        $this->true($handle->is_exhausted());
    }, "Handle exhaustion");

    $this->test(function(){
        $handle = new prggmr\Handle(function($a){
            $this->equal($a, 'Two');
        });
        $handle->bind($this);
        $a = "Two";
        $this->equal($a, "Two");
        $handle->params($a);
        $handle();
        $handle = new prggmr\Handle(function($zero, $one, $two){
            $this->equal($zero, 0);
            $this->equal($one, 1);
            $this->equal($two, 2);
        });
        $handle->bind($this);
        $handle->params([0, 1, 2]);
        $handle();
        $handle = new prggmr\Handle(function($array){
            $this->equal($array, [0, 1, 2]);
        });
        $handle->bind($this);
        $handle->params([[0, 1, 2]]);
        $handle();
    }, "Handle parameters");

});