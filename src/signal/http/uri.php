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

// Define the base REQUEST_URI
if (!defined('REQUEST_URI')) {
    $request = $_SERVER['REQUEST_URI'];
    $pos = strpos($request, '?');
    if ($pos) {
        $request = substr($request, 0, $pos);
    }
    define('REQUEST_URI', $request);
}

 /**
  * Signal HTTP Request URI's to a handle.
  * URIs are matched using the "/path/:param" syntax.
  * Parameters by default allow for any alphanumeric and _-+ chars.
  * 
  * The $_SERVER['REQUEST_URI'] and $_SERVER['REQUEST_METHOD'] are used
  * for checking the signal.
  * 
  * Uses the matching pattern from
  * http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/
  * 
  * URI Routes are constructed using :name based parameters and allow for the
  * following syntax
  * 
  * Standard regex match for URI's
  * -------
  * Uri('/blog/:user/:post')
  * 
  * Matches
  * -------
  * /blog/a_user/a-post-title-here
  * 
  * Custom regex for parameters
  * -------
  * Uri(['/blog/:user/:post', ['user'=>'[\w]{4,8}','post'=>'[\d]{1,8}']])
  * 
  * Matches
  * -------
  * /blog/user/4564
  * 
  * Multiple routes for a single handle
  * -------
  * Uri([
  *  '/blog/:user/:post',
  *  '/post/:user/:post',
  * ])
  * 
  * Matches
  * -------
  * /blog/username/this-is-a-post-title
  * /post/username/this-is-a-post-title
  * 
  * Multiple routes for a single handle w/ custom regex parameters
  * -------
  * Uri([
  *  ['/blog/:user/:post', ['user'=>'[\w]{4,8}','post'=>'[\d]{1,8}']],
  *  ['/post/:user/:post', ['user'=>'[\w]{4,8}','post'=>'[\d]{1,8}']]
  * ])
  * 
  * Matches
  * -------
  * /blog/user/1234
  * /post/user/1234
  * 
  */
class Uri extends \prggmr\signal\Complex {

    /**
     * Flag if the URI matches the requested URI.
     */
    protected $_is_match = false;

    /**
     * Routes for this URI.
     */
    protected $_routes = null;

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
        $this->_routes = [];
        if (is_array($uri)) {
            if (is_array($uri[0]) || isset($uri[1]) && !is_array($uri[1])) {
                foreach ($uri as $_route) {
                    $this->_routes[] = $this->_generate_regex($_route);
                }
            } else {
                $this->_routes[] = $this->_generate_regex($uri);
            }
        } else {
            $this->_routes[] = $this->_generate_regex($uri);
        }
        // skip regex check if method is not allowed
        if (!in_array($_SERVER['REQUEST_METHOD'], $method)) {
            return $this;
        }
        foreach ($this->_routes as $_route) {
            if (preg_match('@^' . $_route[0] . '$@', REQUEST_URI, $matches)) {
                array_shift($matches);
                foreach($_route[1] as $index => $value) {
                    $this->_vars[substr($value,1)] = urldecode($matches[$index]);
                }
                $this->_is_match = true;
            }
        }
    }

    /**
     * Builds a regex string for the URI matching.
     * 
     * @param  string|array  $uri  URI string to match or array for conditionals
     * 
     * @return  string
     */
    protected function _generate_regex($conditions) 
    {
        if (is_array($conditions)) {
            $uri = array_shift($conditions);
            $conditions = $conditions[0];
        } else {
            $uri = $conditions;
            $conditions = [];
        }
        if (!is_array($conditions)) $conditions = [];
        preg_match_all('@:([\w]+)@', $uri, $params, PREG_PATTERN_ORDER);
        $regex = preg_replace_callback('@:[\w]+@', function($matches) use ($conditions) {
            $key = str_replace(':', '', $matches[0]);
            if (isset($conditions[$key])) {
                return '('.$conditions[$key].')';
            } else {
                return '([a-zA-Z0-9_\+\-%]+)';
            }
        }, $uri);
        $regex .= '/?';
        return [$regex, $params[0]];
    }

    public function routine($history = null) 
    {
        if ($this->_is_match) {
            if (null === $this->_event) {
                $this->_event = new Event();
            }
            if (false !== $this->_event) {
                $this->_event->set_uri(REQUEST_URI);
            }
            return [null, ENGINE_ROUTINE_SIGNAL, null];
        }
        return null;
    }

    public function evalute($signal = null) 
    {
        if ($this->_is_match) {
            if (null !== $this->_vars) {
                return $this->_vars;
            }
            return true;
        }
        return false;
    }
}