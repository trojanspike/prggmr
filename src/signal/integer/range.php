<?php
namespace prggmr\signal\integer;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Allows for handles based on a numerical range.
  */
class Range extends \prggmr\signal\Complex {

    /**
     * Constructs a new range signal object.
     *
     * @param  int  $min  Range Min
     * @param  int  $max  Range Max
     * 
     * @return  void
     */
    public function __construct($min, $max)
    {
        if (!is_int($min) || !is_int($max)) {
            throw new \InvalidArgumentException(
                'Range signal requires ints for min and max'
            );
        }
        $this->_info = [$min, $max];
    }
    
    /**
     * Compares the event signal using min max comparison.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  boolean|mixed  False|Signal
     */
    public function evaluate($signal = null)
    {
        $signal = $signal + 0;
        if (!is_int($signal)) {
            return false;
        }
        $min = $this->_info[0];
        $max = $this->_info[1];
        if ($signal >= $min && $signal <= $max) {
            return $signal;
        }
        return false;
    }
}