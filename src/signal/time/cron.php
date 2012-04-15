<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Cron signal
 *
 * Signal event based on the UNIX cron definition
 */
class Cron extends \prggmr\signal\Interval {

    /**
     * Milliseconds elasped before signaling.
     * 
     * @var  integer
     */
    protected $_time = null;

    /**
     * Constructs a cron signal.
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
        return [$return, $this->_info - $current];
    }
}