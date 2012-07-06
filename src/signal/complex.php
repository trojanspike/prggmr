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
 * Complex signals are any signals that perform a calculation to determine
 * signals to trigger, idle time or an idle function.
 */
abstract class Complex extends Standard {

    /**
     * Custom event object for this signal.
     * 
     * @var  object
     */
    protected $_event = null;

    /**
     * The routine object that will be returned to the engine.
     */
    protected $_routine = null;

    /**
     * Constructs a new complex signal.
     *
     * This must be called from a child signal.
     */
    public function __construct()
    {
        $this->_routine = new Routine();
    }

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
     * Runs the routine calculations which allows for a complex signal to 
     * analyze the event history or perform any other computable algorithm
     * for determining when a signal should trigger, the engine should idle or
     * the engine run the given function for a certain amount of time.
     * 
     * The goal of running routine calculations is to allow for complex event
     * processing.
     *
     * The return of this method is ignored.
     * 
     * @param  array  $history  Event history
     * 
     * @return  void
     */
    abstract public function routine($history = null);

    /**
     * Returns the routine object for this complex signal.
     * 
     * @return  object  prggmr\signal\Routine
     */
    final public function get_routine()
    {
        return $this->_routine;
    }

    /**
     * Returns the event assigned to this signal.
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