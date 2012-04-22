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

 require_once 'event.php';

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
     * @param  array  $vars  Additional variables to pass the handle.
     * @param  object  $event  prggmr\signal\http\Event object
     * @param  string  $uri  REQUEST_URI to use. Defaults to _SERVER given.
     */
    public function __construct($url, $method = null, $vars = null, $event = null, $uri = null) 
    {
        if (null !== $event && $event instanceof Event) {
            $this->_event = $event;
        }
        if (null === $uri) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        if (null === $method) {
            $method = ['GET', 'POST'];
        } elseif (!is_array($method)) {
            $method = [$method];
        }
        $this->_info = [
            '#'.preg_replace('#:([\w]+)#i', '(?P<$1>[\w\-_+]+)', $url)."$#i",
            $method,
            str_replace(BASE_URI, '', $uri)
        ];
    }

    public function routine($history = null) 
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $this->_info[1])) {
            return false;
        }
        if (preg_match($this->_info[0], $this->_info[2], $matches)) {
            array_shift($matches);
            if (count($matches) != 0) {
                foreach ($matches as $_k => $_v) {
                    if (is_string($_k)) {
                        unset($matches[$_k]);
                    }
                }
                $this->_vars = array_merge((array) $this->_vars, $matches);
            }
            if (null === $this->_event) {
                $this->_event = new Event();
            }
            if (false !== $this->_event) {
                $this->_event->set_uri($this->_info[2]);
            }
            return [ENGINE_ROUTINE_SIGNAL, null];
        }
    }
}