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
 * The signal object represents the signal passed to the engine
 * to fire a subscription queue.
 */
interface SignalInterface {

    /**
     * Compares the event signal given aganist itself.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches. String/Array
     *          return results found via the match.
     */
    public function compare($signal);

    /**
     * Returns if this signal returns an indexable value.
     *
     * @return  boolean
     */
    public function canIndex();
}

/**
 * The default signal object allows for signals of any type requiring only
 * that they evaluate to true on a strict comparison check.
 */
class Signal implements SignalInterface {

    /**
     * Chain signal
     *
     * @var  array
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
     * @param  mixed  $signal  Signal
     *
     * @return  \prggmr\Queue
     */
    public function __construct($signal)
    {
        $this->_signal = $signal;
    }

    /**
     * Compares the variable given with the signal.
     *
     * @param  mixed  $compare  Variable to compare
     *
     * @return  boolean
     */
    public function compare($compare)
    {
        return ($this->_signal === $compare);
    }

    /**
     * Returns the signal.
     *
     * @return  mixed  signal.
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
     * Establishes a chain link between the given signal.
     *
     * @param  mixed  $signal  Signal
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
     * Removes a chain link from the given signal.
     *
     * @param  mixed  $signal  Signal
     *
     * @return  void
     */
    public function removeChain($signal)
    {
        // does it exist?
        if (null === $this->_chain) return null;
        if (null === ($key = array_search($signal, $this->_chain))) return null;
        unset($this->_chain[$key]);
        if (0 === count($this->_chain)) {
            $this->_chain = null;
        } else {
            // reindex the array starting at 0
            // really should be a better way to do this
            $this->_chain = array_values($this->_chain);
        }
    }

    /**
     * Returns if signal references an indexable value.
     *
     * @return  boolean
     */
    public function canIndex()
    {
        return Engine::canIndex($this->_signal);
    }
}