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
 * Signal event based on timed intervals.
 */
class Interval extends \prggmr\signal\time\Timeout {

    /**
     * Milliseconds elasped before signaling.
     * 
     * @var  integer
     */
    protected $_time = null;

    /**
     * Constructs a time signal.
     *
     * @param  int  $time  Microseconds before signaling.
     *
     * @throws  InvalidArgumentException
     *
     * @return  void
     */
    public function __construct($time, $vars = null)
    {
        parent::__construct($time, $vars);
        $this->_time = $time;
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
        $return = null;
        if ($current >= $this->_info) {
            $this->_info = $this->_time + milliseconds();
            $return = ENGINE_ROUTINE_SIGNAL;
        }
        return [null, $return, $this->_info - $current];
    }
}