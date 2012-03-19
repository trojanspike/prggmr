<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Regex signal
 *
 * Allows for signals using regular expresssions and :var param
 * query strings
 */
class Regex extends \prggmr\signal\Complex {

    /**
     * Constructs a regular expression signal.
     * Support for :name parameters are supported.
     *
     * @param  string  $signal  Regex event signal
     * @param  mixed  $chain  An additional signal for a chain
     *
     * @return  void
     */
    public function __construct($signal)
    {
        $regex = preg_replace('#:([\w]+)#i', 'fix\(?P<$1>[\w_-]+fix\)', $signal);
        $regex = str_replace('fix\(', '(', $regex);
        $regex = str_replace('fix\)', ')', $regex);
        $regex = '#' . $regex . '$#i';
        $this->_signal = $regex;
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
    public function evalute($signal)
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
}