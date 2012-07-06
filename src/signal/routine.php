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
 * This is the object returned to the engine after a routine calculation is run,
 * to allow the engine to determine idle time, signals to dispatch or an
 * idle function.
 */
final class Routine {

    /**
     * Signals to trigger.
     *
     * @var  null|array
     */
    protected $_signals = null;

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
     * Returns a function for the engine to run during idle.
     *
     * @return  null|closure
     */
    final public function get_idle_function(/* ... */)
    {
        return $this->_idle_function;
    }

    /**
     * Sets the idle time for the signal.
     *
     * @return  void
     */
    final public function set_idle_time($time)
    {
        $this->_idle_time = $time;
    }

    /**
     * Sets a function for the engine to run during idle.
     *
     * @param  closure  $function  Function to run to idle.
     * 
     * @return  void
     */
    final public function set_idle_function($function)
    {
        $this->_idle_function = $function;
    }
}