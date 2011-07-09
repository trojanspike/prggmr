<?php
namespace prggmr;
/**
 *  Copyright 2010 Nickolas Whiting
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
 * @author  Nickolas Whiting  <me@nwhiting.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010 Nickolas Whiting
 */

/**
 * The default signal object allows for signals of any type requiring only
 * that they evalute to true on a strict comparison check, otherwise meaning
 * each signal must be exactly equal both in type and value.
 */
class Signal implements SignalInterface {

    /**
     * Chain signal
     *
     * @var  mixed
     */
    protected $_chain = null;

    /**
     * Identifier for signal.
     *
     * @var  string|integer
     */
    protected $_id = null;

    /**
     * Constructs a new signal object.
     *
     * @param  mixed  $signal  Event signal
     *
     * @return  \prggmr\Queue
     */
    public function __construct($signal)
    {
        $this->_signal = $signal;
    }

    /**
     * Compares the event signal given with itself.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches. String/Array
     *          return results found via the match.
     */
    public function compare($signal)
    {
        return ($this->_signal === $signal);
    }

    /**
     * Returns the signal.
     *
     * @return  mixed  Event signal.
     */
    public function signal(/* ... */)
    {
        return $this->_signal;
    }

    /**
     * Returns the signal chain.
     *
     * @return  mixed
     */
    public function getChain(/* ... */)
    {
        return $this->_chain;
    }

    /**
     * Sets the signal chain
     *
     * @param  mixed  $signal  Chain signal
     *
     * @return  void
     */
    public function setChain($signal)
    {
        if (null === $this->_chain) {
            $this->_chain = array();
        }
        $this->_chain[] = $signal;
    }

    /**
     * Removes a signal chain.
     *
     * @param  mixed  $signal  Chain signal
     *
     * @return  void
     */
    public function delChain($signal)
    {
        // does it exist?
        if (null === $this->_chain) return null;
        if (null === ($key = array_search($signal, $this->_chain))) return null;
        unset($this->_chain[$key]);
        if (0 === count($this->_chain)) {
            $this->_chain = null;
        } else {
            // reindex the array starting at 0
            // relly should be a better way to do this
            $this->_chain = array_values($this->_chain);
        }
    }

    /**
     * Returns if this signal returns an indexable value.
     *
     * @return  boolean
     */
    public function canIndex()
    {
        return Engine::canIndex($this->_signal);
    }
}