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

use \InvalidArgumentException;

/**
 * As of v0.3.0 the default signal object allows for signals of only strings
 * and integers. 
 * 
 * Other signal types such as regex are considered "complex" and use the
 * \prggmr\signal\Complex class.
 */
class Signal implements \prggmr\signal\SignalInterface {

    /**
     * Chain signal
     *
     * @var  array
     */
    protected $_chain = null;

    /**
     * Event the signal represents.
     *
     * @var  string|integer
     */
    protected $_signal = null;

    /**
     * Constructs a new signal.
     *
     * @param  string|integer  $signal  Signal
     *
     * @return  void
     */
    public function __construct($signal)
    {
        if (!is_int($signal) && !is_string($signal)) {
            throw new \InvalidArgumentException(
                'Invalid signal type given'
            );
        }
        $this->_signal = $signal;
    }

    /**
     * Evalutes if the given variable matches this signal.
     *
     * @param  string|integer  $var  Variable to evalute.
     *
     * @return  boolean
     */
    public function evaluate($var)
    {
        return ($this->_signal === $var);
    }

    /**
     * Returns the signal.
     *
     * @return  mixed  signal.
     */
    public function getSignal(/* ... */)
    {
        return $this->_signal;
    }

    /**
     * TODO
     * 
     * Fix the below! 
     */

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
}