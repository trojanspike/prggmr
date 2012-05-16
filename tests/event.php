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
        $this->event = new prggmr\Event();
    });

    $this->teardown(function(){
        unset($this->event);
    });

    $this->test(function(){
        $this->event->set_signal('test');
        $this->equal($this->event->get_signal(), 'test');
        $this->event->set_state(STATE_RUNNING);
        $this->false($this->event->set_signal('test_2'));
        $this->event->set_state(STATE_RECYCLED);
        $this->event->set_signal('test_3');
        $this->equal($this->event->get_signal(), 'test_3');
    }, "Event set/get signal");

    $this->test(function(){
        $this->event->set_result(true);
        $this->true($this->event->get_result());
        $this->event->set_result(false);
        $this->false($this->event->get_result());
    }, "Event set/get result");

    $this->test(function(){
        $this->equal($this->event->get_state(), STATE_DECLARED);
        $this->event->halt();
        $this->equal($this->event->get_state(), STATE_HALTED);
    }, "Event halt");

    $this->test(function(){
        $parent = new prggmr\Event();
        $this->false($this->event->is_child());
        $this->event->set_parent($parent);
        $this->true($this->event->is_child());
        $this->equal($this->event->get_parent(), $parent);
        $this->event->set_parent($this->event);
        $this->equal($this->event->get_parent(), $this->event);
    }, "Event parent/children");

    $this->test(function(){
        $this->exception('LogicException', function(){
            $this->event->a++;
        });
        $this->event->a = "Test";
        $this->true(isset($this->event->a));
        $this->equal($this->event->a, "Test");
        unset($this->event->a);
        $this->false(isset($this->event->a));
        $this->exception('LogicException', function(){
            $this->event->a++;
        });
    }, "Event set/get/unset/isset data");
    
});