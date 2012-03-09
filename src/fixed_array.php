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

use \SplFixedArray, \BadMethodCallException;

/**
 * The prggmr FixedArray is an extension to SPL to handle the allocation.
 *
 * FixedArray uses push, pop, shift, unshift for management, offsetSet, __set 
 * and __get are disallowed.
 * 
 * gc is provided as a cleanup and will remove any node with a value of NULL.
 * 
 * The object itself consumes roughly 1 + n(s) KB, this
 * provides a small decrease in size and increase in performance over a 
 * non fixed array.
 */
class FixedArray extends \SplFixedArray {

    /**
     * Pushes a new node(s), increasing the size n*1.
     *
     * @return  object  FixedArray
     */
    public function push(/* ... */) 
    {
        $nodes = func_get_args();
        $len = count($nodes);
        if ($len != 0) {
            $index = $size = $this->getSize();
            $this->setSize($size + $len);
            foreach ($nodes as $_node) {
                parent::offsetSet($index, $_node);
                $index++;
            }
        }

        return $this;
    }

    /**
     * Removes and return the last node in array.
     * 
     * @return  mixed
     */
    public function pop(/* ... */)
    {
        $size = $this->getSize();
        if ($size == 0) return null;
        $node = $this->offsetGet($size - 1);
        parent::offsetUnset($size - 1);
        $this->setSize($size - 1);
        return $node;
    }

    /**
     * Removes and returns the first node in the array.
     * 
     * @return  mixed
     */
    public function shift(/* ... */)
    {
        $size = $this->getSize();
        if ($size == 0) return null;
        $node = $this->offsetGet(0);
        parent::offsetUnset(0);
        $this->setSize($size - 1);
        return $node;
    }

    /**
     * Prepends the given nodes to the beginning of the array.
     * This will reset the index of all nodes.
     * 
     * @return  object  FixedArray
     */
    public function unshift(/* ... */)
    {
        $nodes = array_merge(func_get_args(), $this->toArray());
        $this->flush();
        return call_user_func_array(array($this, 'push'), $nodes);
    }

    /**
     * Collects and destroys garbage nodes.
     * 
     * @return  object  FixedArray
     */
    public function gc(/* ... */)
    {
        $nodes = $this->toArray();
        $this->flush();
        array_map(function($node){
            if (null !== $node) {
                $this->push($node);
            }
        }, $nodes);
        return $this;
    }

    /**
     * Flushes all nodes.
     * 
     * This is only a shortcut to setSize(0);
     *
     * @return  void
     */
    public function flush(/* ... */)
    {
        $this->setSize(0);
    }

    /**
     * Checks for a node at the given index.
     * 
     * @param  integer  $index  Index of node
     * 
     * @return  boolean
     */
    public function __isset($index) 
    {
        return $this->offsetExists($index);
    }

    /**
     * Disallow method calls.
     * 
     * @throws  BadMethodCallException  If called.
     */
    public function offsetSet($index, $data = null)
    {
        throw new \BadMethodCallException(
            'Method disallowed.'
        );
    }
    public function __set($key, $value) 
    {
        throw new \BadMethodCallException(
            'Method disallowed.'
        );
    }
    public function __get($key) 
    {
        throw new \BadMethodCallException(
            'Method disallowed.'
        );
    }
}