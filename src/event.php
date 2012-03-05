<?php
namespace prggmr;
/**
 *  Copyright 2010-12 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *
 * @author  Nickolas Whiting  <prggmr@gmail.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
 */

/**
 * Event
 *
 * Represents an executed/executable prggmr event.
 *
 * As of v0.3.0 the event now extends the State object.
 */

class Event extends State
{
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
     * Backtrace of where this event was fired from only during debugging.
     *
     * @var  array  $trace
     */
    protected $_trace = array();

    /**
     * Constructs a new event object.
     */
    public function __construct()
    {
        $this->setState(static::STATE_DECLARED);
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
        return unset($this->_data[$key]);
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
     * Get event chain.
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
     * Set an event chain.
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
