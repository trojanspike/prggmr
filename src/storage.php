<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Storage trait.
 * 
 * The Storage trait is designed to allow objects to act as a storage, the
 * trait only provides an interface to the normal PHP functions used for
 * transversing an array, keeping all data within a central storage.
 * 
 * See the PHP Manual for more information regarding the functions used
 * in this trait.
 */
trait Storage {
    /**
     * The data storage.
     *
     * @var  array
     */
    protected $_storage = [];

    /**
     * Returns the current storage array.
     * 
     * @return  array
     */
    public function storage(/* ... */)
    {
        return $this->_storage;
    }

    /**
     * Merge an array with the current storage.
     * 
     * @return  void
     */
    public function merge($array)
    {
        $this->_storage = array_merge($this->_storage, $array);
    }

    /**
     * Apply the given function to every node in storage.
     * 
     * @param  callable  $func
     * 
     * @return  void
     */
    public function walk($func)
    {
        return array_walk($func, $this->_storage);
    }

    /**
     * Procedures.
     */
    public function count(/* ... */)
    {
        return count($this->_storage);
    }
    public function current(/* ... */) 
    {
        return current($this->_storage);
    }
    public function end(/* ... */)
    {
        return end($this->_storage);
    }
    public function key(/* ... */)
    {
        return key($this->_storage);
    }
    public function next(/* ... */) 
    {
        return next($this->_storage);
    }
    public function prev(/* ... */)
    {
        return prev($this->_storage);
    }
    public function reset(/* ... */) 
    {
        return reset($this->_storage);
    }
    public function valid(/* ... */)
    {
        return $this->current() !== False;
    }
    public function sort(/* ... */)
    {
        return sort($this->_storage);
    }
    public function usort($cmp)
    {
        return usort($this->_storage, $cmp);
    }
}