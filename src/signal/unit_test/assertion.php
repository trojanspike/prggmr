<?php
namespace prggmr\signal\unit_test;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Assertions class used within a test sig handler.
 * 
 * The assertions class stores all assertions which can be called.
 */
class Assertions {

    use \prggmr\Storage, \prggmr\Singleton;

    const UNKNOWN_ASSERTION = 0xA330;

    /**
     * Adds a new assertion function.
     * 
     * @param  closure  $function  Assertion function
     * @param  string  $name  Assertion name
     * @param  string  $message  Message to return on failure.
     * 
     * @return  void
     */
    public function create_assertion($function, $name, $message = null) 
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(
                'assertion name must be a string'
            );
        }
        if (!$function instanceof \Closure) {
            throw new \InvalidArgumentException(
                'assertion function must be a closure'
            );
        }
        $this->_storage[$name] = [$function, $message];
    }

    /**
     * Calls an assertion function.
     * 
     * @param  string  $name  Assertion function name
     * @param  array  $vars  Array of variables to pass the handler.
     * 
     * @return  boolean|string|int  True on success, False on failure|
     *                              String indicated failure message|
     *                              Integer on unknown assertion.
     */
    public function call_assertion($name, $vars = null)
    {
        if (!isset($this->_storage[$name]) {
            return self::UNKNOWN_ASSERTION;
        }
        $test = call_user_func_array($this->_storage[$name][0], $vars);
        if ($test === true) {
            return true;
        }
        if (null !== $this->_storage[$name][1]) {
            return call_user_func_array('sprintf', $this->_storage[$name][1]);
        }
        return false;
    }
}