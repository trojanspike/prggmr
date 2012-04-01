<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
 * Handle signals using regular expresssions using :var param query strings
 */
class Query extends \prggmr\signal\Complex {

    /**
     * Constructs a regex signal using :var style parameters.
     *
     * @param  string  $query  Variable querystring for capturing signal.
     *
     * @return  void
     */
    public function __construct($query)
    {
        $regex = preg_replace('#:([\w]+)#i', 'fix\(?P<$1>[\w]+fix\)', $query);
        $regex = str_replace('fix\(', '(', $regex);
        $regex = str_replace('fix\)', ')', $regex);
        $regex = '#' . $regex . '$#i';
        $this->_info = $regex;
    }

    /**
     * Evalutes a signal with the object regex.
     *
     * @param  mixed  $signal  Signal to perform regex aganist.
     *
     * @return  boolean|array  Boolean|Array if matches found
     */
    public function evaluate($signal)
    {
        if (preg_match($this->_info, $signal, $matches)) {
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