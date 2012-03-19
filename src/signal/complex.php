<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use \LogicException;

/**
 * Added in v0.3.0
 * 
 * Complex signals are anything that is not a string or integer and requires
 * anything but a simple comparison (===) for evaluation.
 */
class Complex extends \prggmr\Signal {
    /**
     * Force implementation of a new constructor.
     */
    public function __construct($signal)
    {
        throw new \LogicException(
            'Signal not implemented properly'
        );
    }
}