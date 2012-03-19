<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Array contains signal fires true when the given variable is contained 
  * within the given array allowing for strict mode.
  */
class ArrayContainsSignal extends \prggmr\signal\Complex {

    /**
     * Use strict mode.
     *
     * @var  boolean
     */
    private $this->_strict = false;

    /**
     * Constructs a new array contains signal object.
     *
     * @param  mixed  $signal  Signal
     * @param  boolean  $strict  Use strict mode
     */
    public function __construct($signal, $strict = false)
    {
        $this->_strict = $strict;
        $this->_signal = $signal;
    }
    
    /**
     * Compares the event signal using array_search
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches.
     */
    public function evalute($signal)
    {
        if (array_search($signal, $this->_signal, $this->_strict) !== false) {
            return true;
        }
        return false;
    }
}