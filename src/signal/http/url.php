<?php
namespace prggmr\signal\http;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Base URI used to remove any unwanted bytes such as index.php from the 
  * URI.
  */
 if (!defined('BASE_URI')) {
    define('BASE_URI', '');
 }

 /**
  * Signal HTTP Request URL's to a handle.
  * URLs are matched using the "/path/:param" syntax.
  * Parameters by default allow for any alphanumeric and _-+ chars.
  * 
  * The $_SERVER['REQUEST_URI'] and $_SERVER['REQUEST_METHOD'] are used
  * for checking the signal.
  */
class Url extends \prggmr\signal\Complex {

    /**
     * Configures a new URL signal.
     * 
     * @param  string  $url  URL of request to handle.
     * @param  string|array  $method  Type of request to handle.
     * @param  string  $uri  REQUEST_URI to use. Defaults to _SERVER given.
     */
    public function __construct($url, $method = null, $uri = null) 
    {
        if (null === $uri) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        if (null === $method) {
            $method = ['GET', 'POST'];
        } elseif (!is_array($method)) {
            $method = [$method];
        }
        $this->_info = [
            '#'.preg_replace('#:([\w]+)#i', '(?P<$1>[\w]+)', $url)."$#i",
            $method,
            str_replace(BASE_URI, '', $uri)
        ];
    }

    public function routine($history = null) 
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $this->_info[1])) {
            return false;
        }
        if (preg_match($this->_info[0], $this->_info[2], $this->_vars)) {
            array_shift($this->_vars);
            if (count($this->_vars) != 0) {
                foreach ($this->_vars as $_k => $_v) {
                    if (is_string($_k)) {
                        unset($this->_vars[$_k]);
                    }
                }
            }
            return [ENGINE_ROUTINE_SIGNAL, null];
        }
    }
}

 /**
  * Define an API function for this signal.
  */

/**
 * Creates a new prggmr\signal\http\Request sig handler.
 *
 * @param  object  $callable  Closure
 * @param  string  $url  URL to attach the handler.
 * @param  string|array  $method  String or Array of request methods to handle.
 * @param  integer $priority  Handle priority.
 * @param  integer  $exhaust  Handle exhaustion.
 *
 * @return  object|boolean  Handle, boolean if error
 */
function handle_url($closure, $url, $method = null, $priority = null, $exhaust = 1)
{
    return \prggmr::instance()->handle($closure, new Url($url, $method), $priority, $exhaust);
}