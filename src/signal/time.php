<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Time signal
 *
 * Signal event based on time.
 */
class Time extends \prggmr\signal\Complex {

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
        if (!is_int($time) || $time <= 0) {
            throw new \InvalidArgumentException(
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
    public function evaluate($var = null)
    {
        return false;
    }

    /**
     * Determines when the time signal should fire, otherwise returning
     * the engine to idle until it will.
     * 
     * @return  integer
     */
    public function routine($history = null)
    {
        $current = milliseconds();
        if (null === $this->_info) return false;
        if ($current >= $this->_info) {
            $this->_info = null;
            return [ENGINE_ROUTINE_SIGNAL, null];
        }
        return [null, $this->_info - $current];
    }
}