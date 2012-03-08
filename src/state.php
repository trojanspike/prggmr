<?php
namespace prggmr;
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
 * State
 *
 * Added in v0.3.0
 *
 * A State is as it implies, state of a given object, the following states 
 * exist. 
 *
 * STATE_DECLARED
 * The object has been declared.
 *
 * STATE_RUNNING
 * The object is currently running an operation.
 *
 * STATE_EXITED
 * The object has finished execution.
 *
 * STATE_CORRUPTED
 * An error has occurred during object runtime and depending on the recovery
 * it has become corrupted.
 *
 * STATE_RECYCLED
 * The object has successfully ran through a lifecycle and has been recycled for 
 * additional use.
 *
 * STATE_RECOVERED
 * The object became corrupted during runtime execution and recovery was 
 * succesful.
 *
 * STATE_HALTED
 * The object has declared itself as halted to interrupt any further execution.
 */

define('STATE_DECLARED' , 0x00001);
define('STATE_RUNNING'  , 0x00002);
define('STATE_EXITED'   , 0x00003);
define('STATE_CORRUPTED', 0x00004);
define('STATE_RECYCLED' , 0x00005);
define('STATE_RECOVERED', 0x00006);
define('STATE_HALTED'   , 0x00007);

trait State
{
    /**
     * Current state of the object.
     *
     * @var  int
     */
    protected $_state = null;

    /**
     * Returns the current event state.
     *
     * @return  integer  Current state of this event.
     */
    public function getState(/* ... */)
    {
        return $this->_state;
    }

    /**
     * Set the object state.
     *
     * @param  int  $state  State of the object.
     *
     * @throws  InvalidArgumentException
     *
     * @return  void
     */
    public function setState($state) 
    {
        if (!in_array($state, range(1, 7))) {
            throw new \InvalidArgumentException(
                sprintf('Invalid object state %d', $state)
            );
        }

        $this->_state = $state;
    }
}
