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
 * Complex signals are anything that must be evaluted or run in a routine.
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
     * Amount of time to idle the engine.
     *
     * @var  integer|null
     */
    protected $_idle_time = null;

    /**
     * Array of signals to dispatch.
     *
     * @var  array|null
     */
    protected $_dispatch_signals = null;

    /**
     * Function to execute to idle the engine.
     *
     * @var  closure|null
     */
    protected $_idle_function = null;

    /**
     * Compares the event signal given aganist itself.
     *
     * @param  string|integer  $signal  Signal to evaluate
     *
     * @return  boolean|string|array  False on failure. True if matches. String
     *                                or array indicate results to pass handlers
     */
    public function evaluate($signal = null) 
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
     * False indicates the signal can do nothing.
     * 
     * True informs the engine to process this signal for idle or signal 
     * dispatchment.
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
     * Returns an idle time for the signal.
     *
     * @return  integer|null
     */
    final public function get_idle_time(/* ... */)
    {
        return $this->_idle_time;
    }

    /**
     * Returns signals to dispatch.
     *
     * Signals can be returned as either a standard prggmr signal or the complex
     * signal flag. 
     *
     * Signals can set the event's TTL by passing the signal as an array with 
     * node index 1 as the event TTL.
     *
     * @return  array
     */
    final public function get_dispatch_signals(/* ... */)
    {
        return $this->_dispatch_signals;
    }

    /**
     * Returns a function for the engine to run during idle.
     *
     * @return  null|closure
     */
    final public function get_idle_function(/* ... */)
    {
        return $this->_idle_function;
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