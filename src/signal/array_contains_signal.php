<?php
namespace prggmr\signal;
/**
 *  Copyright 2010-12 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *
 * @author  Nickolas Whiting  <prggmr@gmail.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
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