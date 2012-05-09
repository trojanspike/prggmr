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
 * Request URI to use with routing
 */
if (!defined('REQUEST_URI')) {
  define('REQUEST_URI', $_SERVER['REQUEST_URI']);
}

 /**
  * Signal HTTP Request URI's to a handle.
  * URIs are matched using the "/path/:param" syntax.
  * Parameters by default allow for any alphanumeric and _-+ chars.
  * 
  * The $_SERVER['REQUEST_URI'] and $_SERVER['REQUEST_METHOD'] are used
  * for checking the signal.
  */
class Uri extends \prggmr\signal\Complex {

    /**
     * Configures a new URI signal.
     * 
     * @param  string  $uri  URI of request to handle.
     * @param  string|array  $method  Type of request to handle.
     * @param  array  $vars  Additional variables to pass the handle.
     * @param  object  $event  prggmr\signal\http\Event object
     */
    public function __construct($uri, $method = null, $vars = null, $event = null) 
    {
        if (null !== $event && $event instanceof Event) {
            $this->_event = $event;
        }
        if (null === $method) {
            $method = ['GET', 'POST'];
        } elseif (!is_array($method)) {
            $method = [$method];
        }
        if (null !== $vars) {
            if (!is_array($vars)) {
                $vars = [$vars];
            }
            $this->_vars = $vars;
        }
        $base = str_replace(BASE_URI, '', REQUEST_URI);
        $reg = function($str) {
            return '#'.preg_replace('#:([\w]+)#i', '(?P<$1>[\w\-_+]+)', $str)."$#i";
        };
        if (is_array($uri)) {
            $this->_info = [];
            foreach ($uri as $_uri) {
                $this->_info[] = [
                    $reg($_uri),
                    $method,
                    $base
                ];
            }
        } else {
            $this->_info = [[
                $reg($uri),
                $method,
                $base
            ]];
        }
    }

    public function routine($history = null) 
    {
        foreach ($this->_info as $_info) {
            if (!in_array($_SERVER['REQUEST_METHOD'], $_info[1])) {
                return false;
            }
            if (preg_match($_info[0], $_info[2], $matches)) {
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
                    $this->_event->set_uri($_info[2]);
                }
                return [null, ENGINE_ROUTINE_SIGNAL, null];
            }
        }
        return null;
    }

    public function evalute($signal = null) 
    {
        foreach ($this->_info as $_info) {
            if (!in_array($_SERVER['REQUEST_METHOD'], $_info[1])) {
                return false;
            }
            if (preg_match($_info[0], $_info[2], $matches)) {
                array_shift($matches);
                if (count($matches) != 0) {
                    foreach ($matches as $_k => $_v) {
                        if (is_string($_k)) {
                            unset($matches[$_k]);
                        }
                    }
                    return $matches;
                }
                return true;
            }
        }
        return false;
    }
}