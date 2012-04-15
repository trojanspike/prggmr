<?php
namespace prggmr\signal\http;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * An HTTP Event.
 */
class Event extends \prggmr\Event {

    /**
     * The HTTP Request
     * 
     * @var  object
     */
    public $request = null;

    /**
     * The HTTP Response
     * 
     * @var  object
     */
    public $response = null;

    /**
     * Constructs a new HTTP Event.
     * 
     * @return  void
     */
    public function __construct($request, $response = null) 
    {

    }
}