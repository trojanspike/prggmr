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
     * Requested URI
     * 
     * @var  string|null
     */
    protected $_uri = null;

    /**
     * Sets the event URI.
     * 
     * @return  string
     */
    public function set_uri($uri)
    {
        $this->_uri = $uri;
    }

    /**
     * Returns the event URI.
     * 
     * @return  string
     */
    public function get_uri(/* ... */)
    {
        return $this->_uri;
    }
}