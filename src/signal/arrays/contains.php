<?php
namespace prggmr\signal\arrays;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Array contains signal fires true when the given variable is contained 
  * within the given array allowing for strict mode.
  */
class Contains extends \prggmr\signal\Complex {

    /**
     * Use strict mode.
     *
     * @var  boolean
     */
    private $_strict = false;

    /**
     * Constructs a new array contains signal object.
     *
     * @param  array  $info  Array haystack
     * @param  boolean  $strict  Use strict mode
     */
    public function __construct($info, $strict = false)
    {
        $this->_strict = $strict;
        $this->_vars = $vars;
        $this->_info = $info;
    }
    
    /**
     * Compares the event signal using array_search
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches.
     */
    public function evaluate($signal = null)
    {
        if (array_search($signal, $this->_info, $this->_strict) !== false) {
            return $signal;
        }
        return false;
    }
}