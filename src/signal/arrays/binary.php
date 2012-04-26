<?php
namespace prggmr\signal\arrays;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */
 
 /**
  * Allows for handles based on a binary search of an array tree.
  */
class Binary extends \prggmr\signal\Complex {

    /**
     * Constructs a new array binary signal object.
     *
     * @param  array  $info  Array tree
     * @param  callable  $cmp  Function to use for binary search
     * 
     * @return  void
     */
    public function __construct($array, $cmp = null)
    {
        if (!is_array($array) || null !== $cmp && !is_callable($cmp)) {
            throw new \InvalidArgumentException(
                'invalid parameters given array binary'
            );
        }
        $this->_info = $array;
        $this->_cmp = $cmp;
    }
    
    /**
     * Compares the event signal using array_search
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  boolean|mixed  False|Contents of key
     */
    public function evaluate($signal = null)
    {
        $key = bin_search($signal, $this->_info, $this->_cmp);
        if ($key === null) {
            return false;
        }
        return $this->_info[$key];
    }
}