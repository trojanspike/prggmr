<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use \LogicException;

/**
 * Added in v0.3.0
 * 
 * Complex signals are anything that is not a string or integer.
 */
abstract class Complex extends Standard {

    /**
     * Vars assigned to the signal to pass the handler.
     * 
     * @var  array
     */
    protected $_vars = null;

    /**
     * Custom event object for a signal.
     * 
     * @var  object
     */
    protected $_event = null;

    /**
     * Compares the event signal given aganist itself.
     *
     * @param  string|integer  $signal  Signal to evaluate
     *
     * @return  boolean|string|array  False on failure. True if matches. String
     *                                or array indicate results to pass handlers
     */
    public function evaluate($var = null) 
    {
        return false;
    }

    /**
     * Runs the routine calculations which dictate when the engine will run or 
     * idle.
     * 
     * Currently this accepts no parameters in future versions this will 
     * possibly be given a list of events that have taken place, when and their
     * results other signals that are currently registered, when they were 
     * registered etc...
     * 
     * The goal of running routine calculations is to allow for complex event
     * processing.
     * 
     * FALSE indicates the signal can do nothing.
     * 
     * ARRAY informs the engine to signal those events.
     * 
     * @param  array  $history  Event history
     * 
     * @return  boolean|integer|array
     */
    public function routine($history = null)
    {
        return false;
    }

    /**
     * Returns any variables to provide the signal handler.
     * 
     * @return  array|null
     */
    final public function vars(/* ... */)
    {
        return $this->_vars;
    }

    /**
     * Sets or returns the event assigned to this signal.
     * 
     * @return  object|null
     */
    final public function event($event = null)
    {
        if (null === $event) return $this->_event;
        $this->_event = $event;
        return $this->_event;
    }
}