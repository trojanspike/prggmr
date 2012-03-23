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
 * Complex signals are anything that is not a string or integer and requires
 * anything but a simple comparison (===) for evaluation.
 */
abstract class Complex extends \prggmr\Signal {
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
     * Runs the signal routine calculation.
     * 
     * SUBJECT TO CHANGE
     * 
     * @return  boolean|integer
     */
    public function routine()
    {
        return false;
    }
}