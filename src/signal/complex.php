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
     * Force implementation of a new constructor.
     */
    public function __construct($signal)
    {
        throw new \LogicException(
            'Signal not implemented properly'
        );
    }

    /**
     * Compares the event signal given aganist itself.
     *
     * @param  string|integer  $signal  Signal to evaluate
     *
     * @return  boolean|string|array  False on failure. True if matches. String
     *                                or array indicate results to pass handlers
     */
    abstract public function evaluate($var);

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
     * INTEGER informs the engine to idle for that amount of time.
     * 
     * ARRAY informs the engine to signal those events.
     * 
     * @return  boolean|integer|array
     */
    public function routine(/* ... */)
    {
        return false;
    }
}