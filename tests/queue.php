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
        $queue = new prggmr\Queue();
        $queue->enqueue(0);
        $this->equal($queue->count(), 1);
    }, "Queue Enqueue");

    $this->test(function(){
        $queue = new prggmr\Queue();
        $queue->enqueue(0);
        $queue->enqueue(1);
        $this->equal($queue->count(), 2);
        $queue->dequeue(0);
        $this->equal($queue->count(), 1);
    }, 'Queue Dequeue');

    $this->test(function(){
        $this->exception('OverflowException', function(){
            $queue = new prggmr\Queue();
            for ($i=0;$i!=QUEUE_MAX_SIZE+5;$i++) {
                $queue->enqueue(0);
            }
        });
    }, "Queue Max Size Overflow exception");

    $this->test(function(){
        $queue = new prggmr\Queue();
        $queue->enqueue(1, 10);
        $queue->enqueue(2, 11);
        $queue->enqueue(3, 9);
        $queue->sort();
        $this->equal(3, $queue->current()[0]);
        $queue->next();
        $this->equal(1, $queue->current()[0]);
        $queue->next();
        $this->equal(2, $queue->current()[0]);
    }, "Queue Min Sort");

    $this->test(function(){
        $queue = new prggmr\Queue(QUEUE_MAX_HEAP);
        $queue->enqueue(1, 10);
        $queue->enqueue(2, 11);
        $queue->enqueue(3, 9);
        $queue->sort();
        $this->equal(2, $queue->current()[0]);
        $queue->next();
        $this->equal(1, $queue->current()[0]);
        $queue->next();
        $this->equal(3, $queue->current()[0]);
    }, "Queue Max Sort");
    
});
