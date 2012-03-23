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
 * Represents an executed/executable prggmr event signal.
 *
 * As of v0.3.0 the event now inherits the State and Storage traits.
 */

class Event {

    use State, Storage;

    /**
     * Result of the event.
     * 
     * @var  mixed
     */
    protected $_result = null;

    /**
     * Signal event represents.
     * 
     * @var  object
     */
    protected $_signal = null;

    /**
     * Constructs a new event.
     * 
     * @param  null|object  $signal  Signal object or null.
     * 
     * @return  void
     */
    public function __construct($signal = null)
    {
        $this->_signal = $signal;
    }

    /**
     * Sets the result of the event.
     * 
     * @param  mixed  $result
     */
    public function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * Returns the result of the event.
     * 
     * @return  mixed
     */
    public function getResult(/* ... */)
    {
        return $this->_result;
    }

    /**
     * Halts the event execution.
     * 
     * @return  void
     */
    public function halt()
    {
        $this->_state = STATE_HALTED;
    }

    /**
     * Get a variable in the event.
     *
     * @param  mixed  $key  Variable name.
     *
     * @return  mixed|null
     */
    public function __get($key)
    {
        if (isset($this->_storage[$key])) {
            return $this->_storage[$key];
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
        return isset($this->_storage[$key]);
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
        if (isset($key) && strpos($this->__storage[$key], '_') === 1) {
            throw new \LogicException(sprintf(
                "%s is a read-only event property", 
                $key
            ));
        }
        $this->_storage[$key] = $value;
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
        unset($this->_storage[$key]);
    }
}
