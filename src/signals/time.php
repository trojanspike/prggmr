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
 * Time signal
 *
 * Signal events based on time.
 */
class Time extends \prggmr\Signal {

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
        parent::__construct($time);
    }

    /**
     * Compares the event signal given with itself using
     * regular expressions.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches. String/Array
     *          return results found via the match.
     */
    public function compare($signal)
    {
        if (preg_match($this->_signal, $signal, $matches)) {
            array_shift($matches);
            if (count($matches) != 0) {
                foreach ($matches as $_k => $_v) {
                    if (is_string($_k)) {
                        unset($matches[$_k]);
                    }
                }
                return $matches;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns if this signal returns an indexable value.
     * Regex signals cannot be index, this return false always.
     *
     * @return  boolean
     */
    public function canIndex()
    {
        return true;
    }
}