<?php
namespace prggmr\signal\unit_test;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Unit testing signal
 * 
 * This allows for unit testing using signals.
 * 
 * Testing is performed as:
 * 
 * prggmr\signal\unit_test\api\test(function(){
 *     $this->true(true);
 *     $this->false(false);
 *     $this->null(null);
 *     etc ...
 * });
 */
class Test extends \prggmr\signal\Complex {

    /**
     * Constructs a new test signal.
     * 
     * @param  string  $name  Name of the test.
     * 
     * @return  void
     */
    public function __construct($info = null)
    {
        $this->_info = $info;
    }

    /**
     * Routine evaluation.
     */
    public function routine($event_history = null)
    {
        if (null === $this->_event) {
            $this->_event = new Event();
        }
        // test signals always return to fire immediatly
        return [null, ENGINE_ROUTINE_SIGNAL, null];
    }
}