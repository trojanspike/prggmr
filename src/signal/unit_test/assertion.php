<?php
namespace prggmr\signal\unit_test;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Assertions class used within a test sig handler.
 * 
 * The assertions class stores all assertions which can be called.
 */
class Assertions {
    use \prggmr\Storage, \prggmr\Singleton;
}