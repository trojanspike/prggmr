<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use \InvalidArgumentException;

/**
 * As of v0.3.0 the default signal object allows for signals of only strings
 * and integers. 
 * 
 * Other signal types are considered "complex" and use the 
 * \prggmr\signal\Complex object.
 */
class Signal extends \prggmr\signal\Standard {
    /**
     * Constructs a new standard signal.
     *
     * @param  string|integer  $info  Signal information
     *
     * @return  void
     */
    public function __construct($info)
    {
        if (!is_int($info) && !is_string($info)) {
            throw new \InvalidArgumentException(
                'Invalid signal type given'
            );
        }
        $this->_info = $info;
    }
}