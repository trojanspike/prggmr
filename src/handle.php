<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
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
 * Handles are now also a stateful object inheriting the State trait.
 */
class Handle {

    use State;

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
    protected $_exhaustion = null;

    /**
     * Flag determining if the handle has exhausted.
     *
     * @var  boolean
     */
    protected $_exhausted = null;
    
    /**
     * Array of additional parameters to pass the executing function.
     *
     * @var  array
     */
    protected $_params = null;

    /**
     * Constructs a new handle object.
     *
     * @param  mixed  $function  A callable php variable.
     * @param  integer  $exhaust  Count to set handle exhaustion.
     * 
     * @return  void
     */
    public function __construct($function, $exhaust = 1)
    {
        if (!$function instanceof Closure) {
            throw new \InvalidArgumentException(sprintf(
                "handle requires a closure (%s) given",
                (is_object($function)) ?
                get_class($function) : gettype($function)
            ));
        }
        # Invalid or negative exhausting sets the rate to 1.
        if (null !== $exhaust && (!is_int($exhaust) || $exhaust <= -1)) {
            $exhaust = 1;
        }
        // unbind the closure
        $this->_function = $function->bindTo(new \stdClass());
        $this->_exhaustion = $exhaust;
    }

    /**
     * Invoke the handle.
     * 
     * @param  array|mixed  $params  Additional parameters to pass.
     *
     * @return  mixed
     */
    public function __invoke($params = null) 
    {
        # test for exhaustion
        if ($this->is_exhausted()) return true;

        # force array
        if (null === $params) {
            $params = [];
        } elseif (!is_array($params)) {
             $params = [$params];
        }
        
        if (null !== $this->_params) {
            $params = array_merge($params, $this->_params);
        }

        if (null !== $this->_exhaustion) {
            $this->_exhaustion--;
        }

        return call_user_func_array($this->_function, $params);
    }

    /**
     * Returns count until handle becomes exhausted
     *
     * @return  integer
     */
    public function exhaustion(/* ... */)
    {
        return $this->_exhaustion;
    }

    /**
     * Determines if the handle has exhausted.
     *
     * @return  boolean
     */
    public function is_exhausted()
    {
        if (null === $this->_exhaustion) {
            return false;
        }

        if (true === $this->_exhausted) {
            return true;
        }

        if (0 >= $this->_exhaustion) {
            $this->_exhausted = true;
            return true;
        }

        return false;
    }
    
    /**
     * Supply additional parameters to be passed to the handle.
     *
     * @param  mixed  $params  Array of parameters.
     *
     * @return  void
     */
    public function params($params)
    {
        if (!is_array($params)) {
            $params = [$params];
        }
        $this->_params = array_merge((array) $this->_params, $params);
    }

    /**
     * Binds the handle to the given object.
     * 
     * @param  object  $object  Object to bind handle to
     * 
     * @return  void
     */
    public function bind($object)
    {
        $this->_function = $this->_function->bindTo($object);
    }
}