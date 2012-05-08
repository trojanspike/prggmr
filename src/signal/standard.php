<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Standard Signal
 * 
 * Base class for all prggmr signals.
 */
abstract class Standard {

    /**
     * Event the signal represents.
     *
     * @var  string|integer
     */
    protected $_info = null;

    /**
     * Returns the signal information.
     *
     * @return  boolean
     */
    public function info(/* ... */) 
    {
        return $this->_info;
    }
}