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
 */
class Event
{
    /**
     * Event is actively being called.
     */
    const STATE_ACTIVE = 0x01;

    /**
     * Event is inactive and awaiting for its bubble.
     */
    const STATE_INACTIVE = 0x02;

    /**
     * Event has encountered a failure
     */
    const STATE_ERROR = 0x03;

    /**
     * Current state event in which this event is in.
     *
     * @var  int
     */
    protected $_state = Event::STATE_INACTIVE;

    /**
     * Data attached to this event
     *
     * @var  mixed
     */
    protected $_data = null;

    /**
     * Halt the event que after this event finishes.
     *
     * @var  boolean  True to propagate | False otherwise
     */
    protected $_halt = false;

    /**
     * Signal this event represents.
     *
     * @var  object  Signal
     */
    protected $_signal = null;

    /**
     * Subscription event is within.
     *
     * @var  object  Subscription
     */
    protected $_subscription = null;

    /**
     * Message associated with the current event state.
     *
     * @var  string
     */
    protected $_stateMessage = null;

    /**
     * Event which was chained from this event.
     *
     * @var  object  Event
     */
    protected $_chain = null;
    
    /**
     * Backtrace of where this event was fired from, usefull for debugging.
     *
     * @var  array  $trace
     */
    protected $_trace = array();

    /**
     * Internal identifier
     *
     * @var  string 
     */
    private $_id = null;

    /**
     * Constructs a new event object.
     */
    public function __construct()
    {
        // default event state
        $this->setState(self::STATE_INACTIVE);
    }

    /**
     * Sets the event state.
     *
     * @param  integer  $state  State this event is currently in.
     * @param  string  $msg  Message to associate with this event state.
     *
     * @return  boolean  True on success
     */
    public function setState($state, $msg = null)
    {
        $this->_state = (int) $state;
        $this->_stateMessage = $msg;
        return true;
    }

    /**
     * Returns the current event state message.
     *
     * @return  mixed  Current event state message, NULL otherwise.
     */
    public function getStateMessage(/* ... */)
    {
        return $this->_stateMessage;
    }

    /**
     * Returns the current event state.
     *
     * @return  integer  Current state of this event.
     */
    public function getState(/* ... */)
    {
        return $this->_state;
    }

    /**
     * Halts or prevents an event stack including chains.
     *
     * @return  void
     */
    public function halt(/* ... */)
    {
        $this->_halt = true;
    }

    /**
     * Returns the flag to halt the event stack once this
     * event completes execution.
     *
     * @return  boolean  True to halt | False otherwise
     */
    public function isHalted(/* ... */)
    {
        return $this->_halt;
    }

    /**
     * Returns the current data value of this event.
     *
     * This method should also be used to detect if this event currently
     * has data previously attatched by a subscriber in the same stack to
     * avoid overwritting results.
     *
     * @param  mixed  $key  Index key of specific data to return.
     *
     * @return  mixed
     */
    public function getData($key = null)
    {
        if (null !== $key) {
            if (isset($this->_data[$key])) {
                return $this->_data[$key];
            } else {
                return null;
            }
        } else {
            return $this->_data;
        }
    }

    /**
     * Sets data in the event.
     *
     * @param  mixed  $data  Value to set as the result of this event.
     *
     * @return  boolean  True
     */
    public function setData($value, $key = false)
    {

        if (!is_array($this->_data)) {
            (array) $this->_data[$key] = $value;
        } else {
            if (false === $key) {
                $this->_data[] = $value;
            } else {
                $this->_data[$key] = $value;
            }
        }

        return true;
    }

    /**
     * Sets the signal this event represents.
     *
     * @param  object  $signal  Signal
     *
     * @return  void
     */
    public function setSignal($signal)
    {
        $this->_signal = $signal;
    }

    /**
     * Returns the event subscription string this event will bubble upon.
     *
     * @return  string
     */
    public function getSignal(/* ... */)
    {
        return $this->_signal;
    }

    /**
     * Sets the subscription this event is within.
     *
     * @param  object  $subscription  Subscription
     *
     * @return  void
     */
    public function setSubscription($subscription)
    {
        $this->_subscription = $subscription;
    }

    /**
     * Returns the event subscription string this event will bubble upon.
     *
     * @return  string
     */
    public function getSubscription(/* ... */)
    {
        return $this->_subscription;
    }

    /**
     * Sets the chained event.
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

    /**
     * Returns the chained events array.
     *
     * @return  mixed  array, null if no chain exists.
     */
    public function getChain(/* ... */)
    {
        return $this->_chain;
    }
    
    /**
     * Adds a new location from where this event fired from.
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
     * Returns the backtrace information for debugging purposes.
     *
     * @return  array
     */
    public function getTrace(/* ... */)
    {
        return $this->_trace;
    }
}
