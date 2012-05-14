<?php
namespace prggmr\signal\unittest;
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
     * Quick indication of a failure.
     */
    protected $_failed = false;

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
     * 
     * @return  boolean  true
     */
    public function __call($func, $args)
    {
        if ($this->failed()) {
            $this->_output->assertion($this, $func, $args, null);
        } else {
            $call = $this->_assertions->call_assertion($func, $args);
            $this->_output->assertion($this, $func, $args, $call);
        }
        return true;
    }

    /**
     * Checks if the test failed.
     * 
     * @return  boolean
     */
    public function failed()
    {
        return $this->_failed;
    }
}