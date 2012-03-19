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
 * Signal events based on time.
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
        if (!is_int($time) || $time >= 0) {
            throw new InvalidArgumentException(
                "Invalid or negative time given."
            );
        }
        $this->_signal = $time + get_milliseconds();
        $this->start = get_milliseconds();
    }

    
    /**
     * Evalutes if the signal should fire.
     * 
     * @return  boolean
     */
    public function evaluate(/* ... */)
    {
        if ($)
    }
}