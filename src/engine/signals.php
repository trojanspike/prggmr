<?php
namespace prggmr\engine;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Engine Signals.
 * 
 * Represented as hex 0xE001 - 0xE02A
 * 
 * Errors are 0xE002 - 0xE014
 * 
 * Any signals which are directly related to the engine running, e.g. invalid 
 * signal registration, routine calculation failure, handle execution failure, 
 * handle exceptions, loop start and shutdown etc ...
 * 
 * Unrelated signals handled directly by or represented by the engine.
 */
class Signals {
    /**
     * Signal registered within the range of engine signals.
     */
    const RESTRICTED_SIGNAL = 0xE001;
    /**
     * Invalid or unknown callable provided to sig handler.
     */
    const INVALID_HANDLE = 0xE002;
    /**
     * Exception encountered during handle execution.
     */
    const HANDLE_EXCEPTION = 0xE003;
    /**
     * Invalid or unknown signal encountered.
     */
    const INVALID_SIGNAL = 0xE004;
    /**
     * Invalid or unknown event encountered.
     */
    const INVALID_EVENT = 0xE005;
    /**
     * Invalid directory provided for handler loader.
     */
    const INVALID_HANDLE_DIRECTORY = 0xE006;
    /**
     * Infinitly looping recursive event detected.  
     * !! NOT IMPLEMENTED !!
     */
    const INFINITE_RECURSION_LOOP = 0xE007;
    /**
     * Exhausted queue signaled to trigger.
     */
    const EXHAUSTED_QUEUE_SIGNALED = 0xE008;
    /**
     * Engine loop startup
     */
    const LOOP_START = 0xE015;
    /**
     * Engine loop shutdown.
     */
    const LOOP_SHUTDOWN = 0xE016;
    /**
     * Global exception signal.
     */
    const GLOBAL_EXCEPTION = 0xE029;
    /**
     * Global error signal.
     */
    const GLOBAL_ERROR = 0xE02A;
}