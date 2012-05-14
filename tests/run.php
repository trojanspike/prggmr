<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

prggmr\load_signal("unittest");

// load the standard unittest output
prggmr\signal\unittest\api\generate_output();

foreach (glob('*.php') as $file)
{
    include_once ($file);
}
