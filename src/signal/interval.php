<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Timeout signal
 *
 * Signal event based on a timeout.
 */
class Timeout extends \prggmr\signal\Complex {

    /**
     * Constructs a time signal.
     *
     * @param  int  $time  Microseconds before signaling.
     *
     * @throws  InvalidArgumentException
     *
     * @return  void
     */
    public function __construct($time)
    {
        if (!is_int($time) || $time >= 0) {
            throw new InvalidArgumentException(
                "Invalid or negative timeout given."
            );
        }
        $this->_info = $time + milliseconds();
    }

    
    /**
     * Time signals never evalute.
     * 
     * @return  boolean  False
     */
    public function evaluate(/* ... */)
    {
        return false;
    }

    /**
     * Determines when the time signal should fire, otherwise returning
     * the engine to idle until it will.
     * 
     * @return  integer
     */
    public function routine()
    {
        $current = milliseconds();
        if ($current >= $this->_info) {
            $this->_info = null;
            return ENGINE_ROUTINE_SIGNAL;
        }
        return $this->_info - $current;
    }
}