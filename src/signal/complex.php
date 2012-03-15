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