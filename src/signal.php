<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
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