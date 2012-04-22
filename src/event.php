<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('EVENT_SELF_PARENT', -0xE4E);

/**
 * Event
 *
 * Represents an executed/executable prggmr event signal.
 *
 * As of v0.3.0 the event now inherits the State and Storage traits.
 */
class Event {

    use State;

    /**
     * Result of the event.
     * 
     * @var  mixed
     */
    protected $_result = null;

    /**
     * Parent event.
     * 
     * @var  object
     */
    protected $_parent = null;

    /**
     * Signal event represents.
     */
    protected $_signal = null;

    /**
     * Sets the signal for the event.
     * 
     * @param  string|int|object
     * 
     * @return  void
     */
    public function set_signal($signal)
    {
        if ($this->_state !== STATE_DECLARED &&
            $this->_state !== STATE_RECYCLED) {
            return false;
        }
        $this->_signal = $signal;
    }

    /**
     * Returns the event signal.
     * 
     * @return  int|string|object
     */
    public function get_signal(/* ... */)
    {
        return $this->_signal;
    }

    /**
     * Sets the result of the event.
     * 
     * @param  mixed  $result
     */
    public function set_result($result)
    {
        $this->_result = $result;
    }

    /**
     * Returns the result of the event.
     * 
     * @return  mixed
     */
    public function get_result(/* ... */)
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
     * Determines if the event is a child of another event.
     * 
     * @return  boolean
     */
    public function is_child(/* ... */)
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
    public function set_parent(Event $event)
    {
        // Detect if parent is itself to avoid circular referencing
        if ($this === $event) $event = EVENT_SELF_PARENT;
        $this->_parent = $event;
    }

    /**
     * Retrieves this event's parent.
     * 
     * @return  null|object 
     */
    public function get_parent(/* ... */)
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
        throw new \LogicException(sprintf(
            "Call to undefined event property %s",
            $key
        ));
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
        return isset($this->$key);
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
        if (stripos($key, '_') === 0 && isset($this->$key)) {
            throw new \LogicException(sprintf(
                "%s is a read-only event property", 
                $key
            ));
        }
        $this->$key = $value;
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
        if (!isset($this->$key)) return false;
        if (stripos($key, '_') === 0) {
            throw new \LogicException(sprintf(
                "%s is a read-only event property", 
                $key
            ));
        }
        unset($this->$key);
    }
}