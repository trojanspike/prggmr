<?php
namespace prggmr\signal\http\api;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * API can be included to load the entire signal.
 */

use \prggmr\signal\http as http;

/**
 * Attaches a new handle to a URI request.
 * 
 * @param  object  $function  Closure function to execute
 * @param  string  $uri  URI of request to handle.
 * @param  string|array  $method  Type of request to handle.
 * @param  array  $vars  Additional variables to pass the handle.
 * @param  object  $event  prggmr\signal\http\Event object
 * @param  integer|null  $priority  Handle priority
 * @param  integer|null  $exhaust  Handle exhaust rate.
 * 
 * @return  object  prggmr\Handle
 */
function uri_request($function, $uri, $method = null, $vars = null, $event = null, $priority = null, $exhaust = 1) { 
    return \prggmr\handle($function, new http\Uri($uri, $method, $vars, $event), $priority, $exhaust);
}
