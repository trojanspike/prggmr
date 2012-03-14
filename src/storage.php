<?php
namespace prggmr;
/**
 *  Copyright 2010-12 Nickolas Whiting
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *
 * @author  Nickolas Whiting  <prggmr@gmail.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
 */

/**
 * Storage trait.
 * 
 * The Storage trait is designed to allow objects to act as a storage, the
 * trait only provides an interface to the normal PHP functions used for
 * transversing an array, keeping all data within a central storage.
 */
trait Storage {
    /**
     * The data storage.
     *
     * @var  array
     */
    protected $_storage = [];

    /**
     * Storage procedures.
     * 
     * See the PHP Manual for more information regarding these functions.
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