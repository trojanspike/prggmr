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
 * 0xE001 - 0xE015
 * Any signals which are directly related to the engine running, e.g. invalid 
 * signal registration, routine calculation failure, handle execution failure, 
 * handle exceptions, shutdown etc...
 * 
 * 0xE016 - 0xE02A
 * Unrelated signals handled directly by or represented by the engine.
 */
class Signals {
    /**
     * Exception encountered during handle execution.
     */
    const HANDLE_EXCEPTION = 0xE001;
    /**
     * Invalid or unknown callable provided to sig handler.
     */
    const INVALID_HANDLE = 0xE002;
    /**
     * Signal registered within the range of engine signals.
     */
    const RESTRICTED_SIGNAL = 0xE003;
    /**
     * Engine shutdown initiated.
     */
    const SHUTDOWN = 0xE015;
    /**
     * Global exception signal.
     */
    const GLOBAL_EXCEPTION = 0xE016;
    /**
     * Global error signal.
     */
    const GLOBAL_ERROR = 0xE017;
}