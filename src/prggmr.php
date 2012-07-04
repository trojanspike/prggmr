<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

// library version
define('PRGGMR_VERSION', '2.0.0-alpha');

// The creator
define('PRGGMR_MASTERMIND', 'Nickolas Whiting');

// dev mode
if (defined('PRGGMR_DEV_MODE')) {
    error_reporting(E_ALL);
}

$dir = dirname(realpath(__FILE__));

// start'er up
require $dir.'/utils.php';
require $dir.'/singleton.php';
require $dir.'/storage.php';
require $dir.'/state.php';
require $dir.'/engine/signals.php';
require $dir.'/engine.php';
require $dir.'/signal/routines.php';
require $dir.'/signal/standard.php';
require $dir.'/signal.php';
require $dir.'/signal/complex.php';
require $dir.'/signal/time/timeout.php';
require $dir.'/signal/time/interval.php';
require $dir.'/event.php';
require $dir.'/queue.php';
require $dir.'/handle.php';
require $dir.'/api.php';

// debugging mode disabled by default
if (!defined('PRGGMR_DEBUG')) {
    define('PRGGMR_DEBUG', false);
}

// evented exceptions disabled by default
if (!defined('SIGNAL_ERRORS_EXCEPTIONS')) {
    define('SIGNAL_ERRORS_EXCEPTIONS', false);
}
/**
 * The prggmr object works as the global instance used for managing the
 * global engine instance.
 */
final class prggmr extends \prggmr\Engine {

    use prggmr\Singleton;

    /**
     * Initialise the global engine instance.
     *
     * @param  boolean  $event_history  Store a history of all events.
     * @param  boolean  $engine_exceptions  Throw an exception when a error 
     *                                      signal is triggered.
     * 
     * @return  object  prggmr\Engine
     */
    final public static function init($event_history, $engine_exceptions) 
    {
        if (null === static::$_instance) {
            static::$_instance = new self($event_history, $engine_exceptions);
        }
        return static::$_instance;
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

/**
 * Enables prggmr to transform any errors and exceptions into a 
 * catchable signal.
 */
if (SIGNAL_ERRORS_EXCEPTIONS === true) {
    set_error_handler("signal_exceptions");
    set_exception_handler("signal_errors");
}
