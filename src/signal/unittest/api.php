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
 */
function test($function, $name = null) {
    return \prggmr\handle($function, new unittest\Test($name));
}