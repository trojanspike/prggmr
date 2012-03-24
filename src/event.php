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
    protected $_signals = null;

    /**
     * Parent event.
     * 
     * @var  object
     */
    protected $_parent = null;

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
    public function halt(/* ... */)
    {
        $this->_state = STATE_HALTED;
    }

    /**
     * Returns the signal representions this event has been assigned to.
     * Each node is an array containing the signal and time.
     * 
     * [signal, timestamp]
     * 
     * @return  array
     */
    public function signals(/* ... */)
    {
        return $this->_signals;
    }

    /**
     * Adds a signal the event respresents.
     * 
     * @return  null [description]
     */
    public function addSignal($signal)
    {
        if (!is_array($this->_signals)) {
            $this->_signals = [];
        }

        $this->_signals[] = [$signal, milliseconds()];
    }

    /**
     * Determines if the event is a child of another event.
     * 
     * @return  boolean
     */
    public function isChild(/* ... */)
    {
        return null !== $this->_parent;
    }

    /**
     * Sets the parent event.
     * 
     * @param  object  $event  \prggmr\Event
     * 
     * @return  void
     */
    public function setParent(Event $event)
    {
        $this->_parent = $event;
    }

    /**
     * Retrieves this event's parent.
     * 
     * @return  null|object 
     */
    public function parent(/* ... */)
    {
        return $this->_parent;
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
