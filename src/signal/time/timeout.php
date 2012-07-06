<?php
namespace prggmr\signal\time;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Time signal
 *
 * Trigger a signal based on a timeout.
 */
class Timeout extends \prggmr\signal\Complex {

    /**
     * Variables to pass the timeout handle.
     * 
     * @var null|array
     */
    protected $_vars = null;

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
            $this->add_dispatch_signal($this);
        } else {
            $this->set_idle_time($this->_info - $current);
        }
        return true;
    }
}