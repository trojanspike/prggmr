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

use \Closure,
    \Exception,
    \RuntimeException;

/**
 * A handle is the function which will execute upon a signal call.
 *
 * Though attached to a signal the object itself contains no
 * information on what a signal even is, it is possible to couple
 * it within the object, but the handle will unknownly receive an
 * event which contains the same.
 *
 * As of v0.3.0 handles are now designed with an exhausting of 1
 * by default, this is done under the theory that any handle which
 * is registered is done so to run at least once, otherwise it wouldn't
 * exist.
 *
 * Handles are now also a stateful object extending the State class.
 */
class Handle extends State {

    /**
     * The function that will execute when this handle is
     * triggered.
     */
    protected $_function = null;

    /**
     * Count before a handle is exhausted.
     *
     * @var  string
     */
    protected $_limit = 0;

    /**
     * The exhaust limit.
     *
     * @var  integer
     */
    protected $_count = 0;

    /**
     * Flag determining if the handle has exhausted.
     *
     * @var  boolean
     */
    protected $_exhausted = null;

    /**
     * String identifier for this handle
     *
     * @var  string
     */
     protected $_identifier = null;

    /**
     * Array of functions to execute pre dispatch
     *
     * @var  object
     */
    protected $_pre = null;

    /**
     * Array of functions to execute post dispatch
     *
     * @var  object
     */
    protected $_post = null;
    
    /**
     * Array of additional parameters to pass the function executing.
     *
     * @var  array
     */
    protected $_params = null;


    /**
     * Constructs a new handle object.
     *
     * @param  mixed  $function  A callable php variable.
     * @param  string  $identifier  Identifier of this handle.
     * @param  integer  $exhaust  Count to set handle exhaustion.
     */
    public function __construct($function, $identifier = null, $exhaust = 1)
    {
        if (null === $identifier) {
            $identifier = rand(0, 100000);
        }
        if (!is_callable($function)) {
            throw new \InvalidArgumentException(sprintf(
                "handle requires a callable (%s) given",
                (is_object($function)) ?
                get_class($function) : gettype($function)
            ));
        }
        # Invalid or negative exhausting sets the rate to 1.
        if (!is_int($exhaust) || $exhaust <= -1) {
            $exhaust = 1;
        }
        $this->_function = $function;
        $this->_identifier = $identifier;
        $this->_limit = $exhaust;
    }

    /**
     * Invoke the handle, since PHP disallows passing by reference this throws
     * a BadMethodCallException.
     *
     * @throws  BadMethodCallException
     *
     * @return  boolean|void
     */
    public function __invoke() 
    {
        throw new \BadMethodCallException(
            'Handles cannot be invoked, use of execute method required.'
        );
    }

    /**
     * Executes this handles function.
     * Allowing for the first parameter as an array of parameters or
     * by passing them directly.
     *
     * @param  array  $params  Array of parameters to pass.
     *
     * @throws  RuntimeException  If an exception is encountered during execution.
     *
     * @return  mixed  Results of handle execution.
     */
    public function execute(&$params = null)
    {
        # test for exhaustion
        if ($this->isExhausted()) return false;
        
        # Increase execution count
        $this->_count++;

        if (count(func_get_args()) >= 2) {
            $params = func_get_args();
        } else {
            # force array
            if (!is_array($params)) {
                $params = array($params);
            }
        }
        
        if (null !== $this->_params) {
            $passparams = array_merge(&$params, &$this->_params);
        } else {
            $passparams = $params;
        }

        # pre fire
        if (null !== $this->_pre) {
            foreach ($this->_pre as $_index => $_func) {
                try {
                    call_user_func_array($_func, $passparams);
                } catch (\Exception $e) {
                    # unset incase we continue
                    unset($this->_pre[$_index]);
                    throw new HandleException(sprintf(
                        'handle pre fire %s Exception 
                         ( %s ) at ( %s : %s )',
                        $this->getIdentifier(),
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine()
                    ), $params[0], $this);
                }
            }
        }

        # handle fire
        try {
            $result = call_user_func_array($this->_function, $passparams);
        } catch (\Exception $e) {
            throw new HandleException(sprintf(
				'handle %s Exception ( %s ) at ( %s : %s )',
                    $this->getIdentifier(),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
			), $params[0], $this);
        }

        # post fire
        if (null !== $this->_post) {
            foreach ($this->_post as $_index => $_func) {
                try {
                    call_user_func_array($_func, $passparams);
                } catch (\Exception $e) {
                    # unset incase we continue
                    unset($this->_pre[$_index]);
                    throw new HandleException(sprintf(
                        'handle post fire %s Exception 
                        ( %s ) at ( %s : %s )',
                        $this->getIdentifier(),
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine()
                    ), $params[0], $this);
                }
            }
        }

        return $result;
    }

    /**
     * Returns exhaustion limit.
     *
     * @return  integer
     */
    public function limit(/* ... */)
    {
        return $this->_limit;
    }

    /**
     * Returns the number of times a handle has executed.
     *
     * @return  integer
     */
    public function count(/* ... */)
    {
        return $this->_count;
    }

    /**
     * Determines if the handle has exhausted.
     *
     * @return  boolean
     */
    public function isExhausted()
    {
        if (null === $this->_exhausted) {

            $limit = $this->limit();
            $count = $this->count();

            if (!is_int($limit) || 0 === $limit) {
                return false;
            }

            if (0 === $count) return false;

            if ($count >= $limit) {
                $this->_exhausted = true;
                return true;
            }

            return false;
        } else {
            return $this->_exhausted;
        }
    }

    /**
     * Returns the handle identifier.
     *
     * @return  string
     */
    public function getIdentifier(/* ... */)
    {
        return $this->_identifier;
    }

    /**
     * Registers a function to execute before executing the handle.
     *
     * @param  object  $closure  Closure
     *
     * @return  void
     */
    public function preFire($closure)
    {
        if (!is_callable($closure)) {
            throw new \InvalidArgumentException(
                'argument $closure is not a valid php callback'
            );
        }
        if (null === $this->_pre) $this->_pre = array();
        $this->_pre[] = $closure;
    }

    /**
     * Registers a function to execute after executing the handle.
     *
     * @param  object  $closure  Closure
     *
     * @return  void
     */
    public function postFire($closure)
    {
        if (!is_callable($closure)) {
            throw new \InvalidArgumentException(
                'argument $closure is not a valid php callback'
            );
        }
        if (null === $this->_post) $this->_post = array();
        $this->_post[] = $closure;
    }
    
    /**
     * Supplies additional parameters during runtime.
     *
     * @param  mixed  $params  Array of runtime parameters.
     *
     * @return  void
     */
    public function params(&$params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }
        
        $this->_params = $params;
    }
}


class HandleException extends \Exception {
    
    /**
     * Event associated with the exception.
     *
     * @param  object  \prggmr\Event
     */
    protected $_event = null;
    
    /**
     * Handle associated with the exception.
     *
     * @param  object  \prggmr\Handle
     */
    protected $_handle = null;
    
    public function __construct($message, \prggmr\Event $event, \prggmr\Handle $handle)
    {
        $this->_event = $event;
        $this->_handle = $handle;
        parent::__construct($message);
    }
    
    /**
     * Returns the event associated with this exception.
     *
     * @return  object  \prggmr\Event
     */
    public function getEvent(/* ... */)
    {
        return $this->_event;
    }
    
    /**
     * Returns the handle associated with this exception.
     *
     * @return  object  \prggmr\Handle
     */
    public function getHandle(/* ... */)
    {
        return $this->_handle;
    }
}
