<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Event
 *
 * Represents an executed/executable prggmr event.
 *
 * As of v0.3.0 the event now extends the State object.
 */

class Event {

    use State;

    /**
     * Data attached to this event
     *
     * @var  mixed
     */
    private $_data = null;

    /**
     * Event chained to this event.
     *
     * @var  object  Event
     */
    protected $_chain = null;
    
    /**
     * Backtrace of this event.
     *
     * @var  array  $trace
     */
    protected $_trace = array();

    /**
     * Get a variable in the event.
     *
     * @param  mixed  $key  Variable name.
     *
     * @return  mixed|null
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    /**
     * Checks for a variable in the event.
     *
     * @param  mixed  $key  Variable name.
     *
     * @return  boolean
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * Set a variable in the event.
     *
     * @param  string  $key  Name of variable
     *
     * @param  mixed  $value  Value to variable
     *
     * @return  boolean  True
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
        return true;
    }

    /**
     * Deletes a variable in the event.
     *
     * @param  mixed  $key  Variable name.
     *
     * @return  boolean
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * Add a new backtrace.
     *
     * @param  array  $trace  PHP trace array.
     *
     * @return  void
     */
    public function addTrace($trace)
    {
        $this->_trace[] = $trace;
    }

    /**
     * Gets the event chain.
     *
     * @return  mixed  array, null if no chain exists.
     */
    public function getChain(/* ... */)
    {
        return $this->_chain;
    }

    /**
     * Returns backtrace information.
     *
     * @return  array
     */
    public function getTrace(/* ... */)
    {
        return $this->_trace;
    }

    /**
     * Sets an event chain.
     *
     * @param  object  $chain  Event
     */
    public function setChain(Event $chain)
    {
        if (null === $this->_chain) {
            $this->_chain = array();
        }
        $this->_chain[] = $chain;
    }
}
