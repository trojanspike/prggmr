<?php
namespace prggmr\signal\unittest\api;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * API can be included to load the entire signal.
 */

use \prggmr\signal\unittest as unittest;

/**
 * Add a new assertion function.
 * 
 * @param  closure  $function  Assertion function
 * @param  string  $name  Assertion name
 * @param  string  $message  Message to return on failure.
 * 
 * @return  void
 */
function create_assertion($function, $name, $message = null) {
    return unittest\Assertions::instance()->create_assertion($function, $name, $message);
}

/**
 * Creates a new test case.
 * 
 * @param  object  $function  Test function
 * @param  string  $name  Test name
 * @param  object  $event  prggmr\signal\unittest\Event
 * 
 * @return  array  [Handle, Signal]
 */
function test($function, $name = null, $event = null) {
    $signal = new unittest\Test($name, $event);
    $handle = \prggmr\handle($function, $signal);
    return [$handle, $signal];
}

/**
 * Constructs a new unit testing suite.
 * 
 * @param  object  $function  Closure
 * @param  object|null  $event  prggmr\signal\unittest\Event
 * 
 * @return  void
 */
function suite($function, $event = null) {
    return new unittest\Suite($function, \prggmr\prggmr(), $event);
}