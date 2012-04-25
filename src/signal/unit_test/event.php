<?php
namespace prggmr\signal\unit_test;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Unit testing event object
 * 
 * The event object contains the test information and assertion functions.
 */
class Event extends \prggmr\Event {
    
    /**
     * Assertion functions
     */
    protected $_assertions = [];

    /**
     * Assertions run and their results.
     */
    protected $_assertion_results = [];

    /**
     * Constructs a new event.
     */
    public function __construct(Assertions $assertions = null, Output $output = null) 
    {
        if (null === $assertions) {
            $this->_assertions = Assertions::instance();
        }
        if (null === $output) {
            $this->_output = Output::instance();
        }
    }

    /**
     * Calls an assertion function.
     */
    public function __call($func, $args)
    {
        $call = $this->_assertions->call_assertion($func, $args);

        if (true === $call) {
            $this->_output->assertion_pass($call);
        }
    }
}