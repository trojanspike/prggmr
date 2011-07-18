<?php
namespace prggmr;
/**
 *  Copyright 2010 Nickolas Whiting
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
 * @author  Nickolas Whiting  <me@nwhiting.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010 Nickolas Whiting
 */

use \Closure,
    \Exception,
    \RuntimeException;

/**
 * The subscriber object is the main object responsible for holding our event
 * bubblers or the function which will execute when our queue says it is time.
 *
 * Though attached to a subscriptton queue the object itself contains no
 * information on what subscription it belongs to, it is possible to couple
 * it into the object if it is really needed but realistically the bubble will
 * recieve of copy of the current event which will ironically contain the
 * subscription object that this event is contained within allowing it to
 * call the event that is currently firing ... and if this seems a bit
 * crazy well thats because it is.
 */
class Subscription {

    /**
     * The lambda function that will execute when this subscription is
     * triggered.
     */
    protected $_function = null;

    /**
     * Count before a subscription is exhausted.
     *
     * @var  string
     */
    protected $_limit = 0;

    /**
     * The exhaust limit
     *
     * @var  integer
     */
    protected $_count = 0;

    /**
     * Flag determaining if the subscription has exhausted.
     *
     * @var  boolean
     */
    protected $_exhausted = null;

    /**
     * String identifier for this subscription
     *
     * @var  string
     */
     protected $_identifier = null;

    /**
     * Array of functions to fire pre dispatch
     *
     * @var  object
     */
    protected $_pre = null;

    /**
     * Array of functions to fire post dispatch
     *
     * @var  object
     */
    protected $_post = null;


    /**
     * Constructs a new subscription object.
     *
     * @param  mixed  $function  A callable variable.
     * @param  string  $identifier  Identifier of this subscription.
     * @param  integer  $exhaust  Count to set subscription exhaustion.
     */
    public function __construct($function, $identifier = null, $exhaust = 0)
    {
        if (null === $identifier) {
            $identifier = rand(0, 100000);
        }
        // TODO: What should be set on an invalid or negative exhaust?
        if (!is_int($exhaust) || $exhaust <= -1) {
            $exhaust = 0;
        }
        $this->_function = $function;
        $this->_identifier = $identifier;
        $this->_limit = $exhaust;
    }

    /**
     * Fires this subscriptions function.
     * Allowing for the first parameter as an array of parameters or
     * by passing them directly.
     *
     * @param  array  $params  Array of parameters to pass.
     *
     * @throws  RuntimeException  When exception thrown within the closure.
     * @return  mixed  Results of the function
     */
    public function fire($params = null)
    {
        // test for exhaustion
        if ($this->isExhausted()) return false;
        $this->_count++;

        if (count(func_get_args()) >= 2) {
            $params = func_get_args();
        } else {
            // force array
            if (!is_array($params)) {
                $params = array($params);
            }
        }

        // pre fire
        if (null !== $this->_pre) {
            foreach ($this->_pre as $_func) {
                try {
                    call_user_func_array($_func, $params);
                } catch (\Exception $e) {
                    throw new \RuntimeException(sprintf(
                        'Subscription pre fire %s failed with error %s',
                        $this->getIdentifier(),
                        $e->getMessage()
                    ));
                }
            }
        }

        // subscription fire
        try {
            return call_user_func_array($this->_function, $params);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
				'Subscription %s failed with error %s',
				$this->getIdentifier(),
				$e->getMessage()
			));
        }

        // post fire
        if (null !== $this->_post) {
            foreach ($this->_post as $_func) {
                try {
                    call_user_func_array($_func, $params);
                } catch (\Exception $e) {
                    throw new \RuntimeException(sprintf(
                        'Subscription post fire %s failed with error %s',
                        $this->getIdentifier(),
                        $e->getMessage()
                    ));
                }
            }
        }
    }

    /**
     * Returns the number of times this subcription is to fire before exhaustion
     * 0 = infinite.
     *
     * @return  integer
     */
    public function limit(/* ... */)
    {
        return $this->_limit;
    }

    /**
     * Returns the number of times a subscription has fired.
     *
     * @return  integer
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Determains if the subscription has exhausted.
     *
     * @return  boolean
     */
    public function isExhausted()
    {
        if (null === $this->_exhausted) {

            $limit = $this->limit();
            $count = $this->count();

            if (!is_int($limit) || 0 === $limit) {
                $this->_exhausted = false;
                return false;
            }

            if (0 === $count) return false;

            if ($this->count() >= $limit) {
                $this->_exhausted = true;
                return true;
            }

            return false;
        } else {
            return $this->_exhausted;
        }
    }

    /**
     * Returns the identifier.
     *
     * @return  string
     */
    public function getIdentifier(/* ... */)
    {
        return $this->_identifier;
    }

    /**
     * Adds a function to trigger before firing the subscription.
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
     * Adds a function to trigger after firing the subscription.
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
}
