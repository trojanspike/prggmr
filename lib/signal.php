<?php
namespace prggmr;
/**
 *  Copyright 2010-11 Nickolas Whiting
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
 * @copyright  Copyright (c), 2010-11 Nickolas Whiting
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
            // really should be a better way to do this
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

/**
 * Regex signal
 *
 * Allows for signals using regular expresssions and :var param
 * query strings
 */
class RegexSignal extends Signal {

    /**
     * Constructs a regular expression signal.
     * Support for :name parameters are supported.
     *
     * @param  string  $signal  Regex event signal
     * @param  mixed  $chain  An additional signal for a chain
     *
     * @return  void
     */
    public function __construct($signal)
    {
        $regex = preg_replace('#:([\w]+)#i', 'fix\(?P<$1>[\w_-]+fix\)', $signal);
        $regex = str_replace('fix\(', '(', $regex);
        $regex = str_replace('fix\)', ')', $regex);
        $regex = '#' . $regex . '$#i';
        parent::__construct($regex);
    }

    /**
     * Compares the event signal given with itself using
     * regular expressions.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches. String/Array
     *          return results found via the match.
     */
    public function compare($signal)
    {
        if (preg_match($this->_signal, $signal, $matches)) {
            array_shift($matches);
            if (count($matches) != 0) {
                foreach ($matches as $_k => $_v) {
                    if (is_string($_k)) {
                        unset($matches[$_k]);
                    }
                }
                return $matches;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns if this signal returns an indexable value.
     * Regex signals cannot be index, this return false always.
     *
     * @return  boolean
     */
    public function canIndex()
    {
        return false;
    }
}