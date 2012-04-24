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
 * prggmr\signal\unit_test\test(function(){
 *     $this->true(true);
 *     $this->false(false);
 *     $this->null(null);
 *     etc ...
 * });
 */