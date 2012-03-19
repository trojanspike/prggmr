<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use \InvalidArgumentException;

/**
 * Defines the maximum number of handlers allowed within a Queue.
 */
if (!defined('QUEUE_MAX_SIZE')) {
    define('QUEUE_MAX_SIZE', 24);
}

/**
 * Defines the default priority of queue nodes
 */
if (!defined('QUEUE_DEFAULT_PRIORITY')) {
    define('QUEUE_DEFAULT_PRIORITY', 100);
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
    public function enqueue($node, $priority = QUEUE_DEFAULT_PRIORITY)
    {
        if ($this->count() > QUEUE_MAX_SIZE) {
            throw new \OverflowException(
                'Queue max size reached'
            );
        }
        $this->_dirty = true;
        if (null === $priority || !is_int($priority)) {
            $priority = QUEUE_DEFAULT_PRIORITY;
        }
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