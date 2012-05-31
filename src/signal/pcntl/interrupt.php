<?php
namespace prggmr\signal\pcntl;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Allow for handling script interruption.
  */
class Interrupt extends \prggmr\signal\Complex 
{
    public function __construct($engine = null) {
        pcntl_signal(SIGINT, function() use ($engine){
            if (null === $engine) {
                \prggmr\signal($this);
            } else {
                $engine->signal($this);
            }
            return true;
        });
    }
}