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
define('PRGGMR_VERSION', '0.3.0B');

// The creator
define('PRGGMR_MASTERMIND', 'Nickolas Whiting');

$dir = dirname(realpath(__FILE__));

// start'er up
require $dir.'/utils.php';
require $dir.'/fixed_array.php';
require $dir.'/engine/signals.php';
require $dir.'/state.php';
require $dir.'/engine.php';
require $dir.'/signal.php';
require $dir.'/event.php';
require $dir.'/queue.php';
require $dir.'/handle.php';
require $dir.'/api.php';

// debugging mode disabled by default
if (!defined('PRGGMR_DEBUG')) {
    define('PRGGMR_DEBUG', false);
}

// evented exceptions disabled by default
if (!defined('PRGGMR_EVENTED_EXCEPTIONS')) {
    define('PRGGMR_EVENTED_EXCEPTIONS', false);
}

/**
 * The prggmr object works as the global instance used for managing the
 * global api and the prggmr signaled exceptions hook.
 */
final class prggmr extends \prggmr\Engine {

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

/**
 * Enables prggmr to transform any errors and exceptions into a 
 * catchable signal.
 */
if (PRGGMR_EVENTED_EXCEPTIONS === true) {
    set_error_handler("signalExceptions");
    set_exception_handler("signalErrors");
}
