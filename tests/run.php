<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

foreach (glob('*.php') as $file)
{
    include_once ($file);
}

prggmr\handle(function(){
    $tests = 0;
    foreach (prggmr\event_history() as $_node) {
        if ($_node[0] instanceof prggmr\signal\unittest\Event) {
            $tests++;
        }
    }
    echo PHP_EOL;
    echo "Ran $tests tests";
    echo PHP_EOL;
}, prggmr\engine\Signals::LOOP_SHUTDOWN);