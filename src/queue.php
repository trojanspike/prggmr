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

use \InvalidArgumentException;

/**
 * Defines the maximum number of handlers allowed within a Queue.
 */
if (!defined('QUEUE_MAX_SIZE')) {
    define('QUEUE_MAX_SIZE', 24);
}

/**
 * Queue Heap Types available.
 * 
 * QUEUE_MIN_HEAP
 * Queue functions as a min-heap.
 * 
 * QUEUE_MAX_HEAP
 * Queue functions as a max-heap.
 */
define('QUEUE_MIN_HEAP', 0xBF01);
define('QUEUE_MAX_HEAP', 0xBF02);

require 'storage.php';

/**
 * As of v0.3.0 Queues no longer maintain a reference to a signal.
 *
 * The Queue is still a representation of a PriorityQueue and will remain so 
 * until the issues with PHP's current implementation are addressed.
 * 
 * The queue can also be explicity set to a MIN or MAX heap upon construction.
 */
class Queue {

    use Storage;

    /**
     * Flag for prioritizing.
     * 
     * @var  boolean
     */
    protected $_dirty = false;

    /**
     * Heap type.
     * 
     * @var  integer
     */
    protected $_type = 0;

    /**
     * Constructs a new queue object.
     *
     * @param  int  $type  Queue type
     *
     * @return  void
     */
    public function __construct($type = QUEUE_MIN_HEAP)
    {
        $this->_type = $type;
    }

    /**
     * Pushes a new handler into the queue.
     *
     * @param  mixed  $node  Variable to store
     * @param  integer $priority  Priority of the callable
     *
     * @throws  OverflowException  If max size exceeded
     *
     * @return  void
     */
    public function enqueue($node, $priority = 100)
    {
        if ($this->count() > QUEUE_MAX_SIZE) {
            throw new \OverflowException(
                'Queue max size reached'
            );
        }
        $this->_dirty = true;
        if (null === $priority || !is_int($priority)) $priority = 100;
        $this->_storage[] = [$node, $priority];
    }

    /**
    * Removes a handle from the queue.
    *
    * @param  mixed  $node  Reference to the node.
    *
    * @throws  InvalidArgumentException
    * @return  boolean
    */
    public function dequeue($node)
    {
        while($this->valid()) {
            if ($this->current()[0] === $node) {
                unset($this->_storage[$this->key()]);
                return true;
            }
            $this->next();
        }
        return false;
    }

    /**
     * Sorts the queue as a MIN or MAX heap.
     *
     * @return  void
     */
    public function sort(/* ... */)
    {
        if (!$this->_dirty) return null;
        $this->usort(function($a, $b){
            if ($this->_type === QUEUE_MAX_HEAP) {
                return $a[1] < $b[1];
            }
            return $a[1] > $b[1];
        });
        $this->_dirty = false;
    }
}