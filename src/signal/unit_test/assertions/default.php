<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use prggmr\signal\unit_test\api as api;

/**
 * Default assertions.
 *
 * Assertions avaliable by default.
 */

/**
 * Case sensitive equals assertion.
 *
 * Asserts that two values are exactly equal.
 */
api\create_assertion(function($expect, $actual){
    if ($expect === $actual) return true;
    return false;
}, 'equal', "%s does not exactly equal %s");

/**
 * Case insensitive equals assertion.
 */
api\create_assertion(function($expect, $actual){
    if ($expect == $actual) return true;
    return false;
}, 'iequal', '%s does not equal %s');

/**
 * Exception assertion.
 *
 * Asserts that the giving code throws the giving Exception.
 */
api\create_assertion(function($exception, \Closure $code){
    try {
        $code();
    } catch (\Exception $e) {
        if (get_class($e) === $exception) return true;
    }
    return false;
}, 'exception', 'Exception %s was not thrown');

/**
 * True assertion.
 *
 * Asserts the provided expression results to true.
 */
api\create_assertion(function($var){
    if ($var === true) return true;
    return false;
}, 'true', '%s does not equal true');

/**
 * False assertion.
 *
 * Asserts the provided expressions results to false.
 */
api\create_assertion(function($var){
    if ($var === false) return true;
    return false;
}, 'false', '%s does not equal false');

/**
 * Null assertion
 *
 * Asserts the given expression results to null.
 */
api\create_assertion(function($var){
    if ($var === null) return true;
    return false;
}, 'null', '%s does not equal null');

/**
 * Array assertion
 *
 * Asserts the given variable is an array.
 */
api\create_assertion(function($array){
    if (is_array($array)) return true;
    return false;
}, 'array', '%s is not an array');

/**
 * String assertion
 *
 * Asserts the given variable is a string.
 */
api\create_assertion(function($string){
    if (is_string($string)) return true;
    return false;
}, 'string', '%s is not a string');

/**
 * Integer assertion
 *
 * Asserts the given variable is a integer.
 */
api\create_assertion(function($int){
    if (is_int($int)) return true;
    return false;
}, 'integer', '%s is not an integer');

/**
 * Float assertion
 *
 * Asserts the given variable is a float.
 */
api\create_assertion(function($float){
    if(is_float($float)) return true;
    return false;
}, 'float', '%s is not a float');

/**
 * Object assertion
 *
 * Asserts the given variable is an object.
 */
api\create_assertion(function($object){
    if (is_object($object)) return true;
    return false;
}, 'object', '%s is not an object');

/**
 * Instanceof assertion
 *
 * Asserts the given object is an instance of the provided class.
 */
api\create_assertion(function($object, $class){
    if (get_class($class) === $object) return true;
    return false;
}, 'instanceof', '%s is not an instance of %s');
