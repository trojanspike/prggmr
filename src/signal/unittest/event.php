<?php
namespace prggmr\signal\unittest;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

if (!defined('SKIP_TESTS_ON_FAILURE')) {
    define('SKIP_TESTS_ON_FAILURE', false);
}

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
     * Assertion tests ran.
     */
    protected $_assertions_ran = []; 

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
        $this->_assertions_ran[] = $func;
        if ($this->_failed && SKIP_TESTS_ON_FAILURE) {
            $this->_output->assertion($this, $func, $args, null);
        } else {
            try {
                $call = $this->_assertions->call_assertion($func, $args);
            } catch (\BadMethodCallException $e) {
                $call = null;
                $this->_output->unknown_assertion(
                    $this, $func, $args, $this->_assertions
                );
            }
            if ($call !== true) {
                $this->_failed = true;
            }
            $this->_output->assertion($this, $func, $args, $call);
        }
        // Add call to results
        $this->_assertion_results[] = [
            $call, $func, $args, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];
        return true;
    }

    /**
     * Returns the assertion results.
     * 
     * @return  array
     */
    public function get_assertion_results()
    {
        return $this->_assertion_results;
    }

    /**
     * Returns the assertions run.
     * 
     * @return  array
     */
    public function get_assertions_ran()
    {
        return $this->_assertions_ran;
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