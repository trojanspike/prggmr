<?php
/**
 *  Copyright 2010-12 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *
 * @author  Nickolas Whiting  <prggmr@gmail.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
 */

// library version
define('PRGGMR_VERSION', '0.2.0');

// The creator
define('PRGGMR_MASTERMIND', 'Nickolas Whiting');

$dir = dirname(realpath(__FILE__));

// start'er up
require $dir.'/engine.php';
require $dir.'/signal.php';
require $dir.'/event.php';
require $dir.'/api.php';
require $dir.'/queue.php';
require $dir.'/subscription.php';

// debugging mode disabled by default
if (!defined('PRGGMR_DEBUG')) {
    define('PRGGMR_DEBUG', false);
}

// evented exceptions disabled by default
if (!defined('PRGGMR_EVENTED_EXCEPTIONS')) {
    define('PRGGMR_EVENTED_EXCEPTIONS', false);
}

// include all signals if avaliable
if (is_dir($dir.'/signals')) {
    foreach (glob($dir.'/signals/*.php') as $_file) {
        $_name = explode('/', $_file);
        $_class = array_map('ucfirst', explode('_', 
            str_replace('.php', '', end($_name))
        ));
        if (!class_exists(implode('', $_class))) {
            include_once $_file;
        }
    }
}

/**
 * The prggmr object is a singleton which allows for a global engine api.
 */
class prggmr extends \prggmr\Engine {
    
    /**
     * prggmr 
     */
    const EXCEPTION = 0xe4;
    
    /**
     * @var  object|null  Instanceof the singleton
     */
    private static $_instance = null;

    /**
     * Returns instance of the prggmr api.
     */
    final public static function instance(/* ... */)
    {
        if (null === static::$_instance) {
            static::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Returns the current version of prggmr.
     *
     * @return  string
     */
    final public static function version(/* ... */)
    {
        return PRGGMR_VERSION;
    }
}

// turns on prggmr error handling
// turning errors into exceptions and exceptions into events
if (PRGGMR_EVENTED_EXCEPTIONS === true) {
    function evented_exceptions($exception) {
        fire(prggmr::EXCEPTION, $exception);
    }
    function evented_error_handler($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    set_error_handler("evented_error_handler");
    set_exception_handler("evented_exceptions");
}