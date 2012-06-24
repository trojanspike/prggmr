<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Routine signal class
 * 
 * Class used to return a list of signals for the engine to trigger during
 * a routine calculation.
 */
final class Routines {

    /**
     * Signals to trigger.
     *
     * @var  null|array
     */
    protected $_signals = null;

    /**
     * Registers a new signal to trigger.
     *
     * @param  int|string|object  $signal  Signal to trigger.
     * @param  null|array  $vars  Variables to pass the sig handler.
     * @param  object|null  $event  Event to use during execution.
     * 
     * @return  boolean
     */
    public function add_signal($signal = null, $vars = null, $event = null) 
    {
        $this->_signals = [$signal, $vars, $event];
    }

    /**
     * Returns the signals registered.
     *
     * @return  array|null
     */
    public function get_signals()
    {
        return $this->_signal;
    }
}