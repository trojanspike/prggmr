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

use \SplObjectStorage,
    \InvalidArgumentException;


/**
 * The queue object is a priority queue implemented using a heap, it was decided
 * against using PHP's implementation of the current PriorityQueue which is not
 * to say it isn't useful, only wasteful. This does come at a disadvantage of
 * sacrificing performance over functionality ... even at a small cost.
 *
 * The priority works as a min-heap, which also brings the point that unlike
 * the implementation in the SPL priority is limited only to integers this is
 * done for performance concerns.
 *
 * The heap is implemented using only the priority, the data is ignored.
 *
 * The Queue maintains handlers attached to a signal.
 */
class Queue extends \SplObjectStorage {

    /**
     * The signal which the queue manages.
     *
     * @var  object  Signal
     */
    protected $_signal = null;

    /**
     * Flag for the prioritizing the queue.
     *
     * @var  boolean
     */
    public $dirty = false;

    /**
     * Constructs a new queue object.
     *
     * @param  object  $signal  Signal
     *
     * @return  \prggmr\Queue
     */
    public function __construct(Signal $signal)
    {
        $this->_signal = $signal;
    }

    /**
     * Returns the signal this queue manages.
     *
     * @param  boolean  $signal  Return the signal rather than the object.
     *
     * @return  object
     */
    public function getSignal($signal = false)
    {
        if (!$signal) {
            return $this->_signal;
        } else {
            return $this->_signal->signal();
        }
    }

    /**
     * Pushes a new handler into the queue.
     *
     * @param  object  $handle  \prggmr\Handle
     * @param  integer $priority  Priority of the handle
     *
     * @return  void
     */
    public function enqueue(Handle $handle, $priority = 100)
    {
        $this->dirty = true;
        if (null === $priority || !is_int($priority)) $priority = 100;
        $priority = $priority;
        parent::attach($handle, $priority);
    }

    /**
    * Removes a handle from the queue.
    *
    * @param  mixed  $handle  Handle instance or identifier.
    *
    * @throws  InvalidArgumentException
    * @return  void
    */
    public function dequeue($handle)
    {
        if (is_string($handle) && $this->locate($handle)) {
            parent::detach($this->current());
            $this->dirty = true;
        } elseif ($handle instanceof Handle) {
            parent::detach($handle);
            $this->dirty = true;
        }
    }

    /**
    * Locates a handle.
    *
    * @param  string  $identifier  Handle identifier.
    *
    * @return  void
    */
    public function locate($identifier)
    {
        $this->rewind(false);
        while($this->valid()) {
            if ($this->current()->getIdentifier() == $identifier) {
                return true;
            }
            $this->next();
        }
        return false;
    }

    /**
     * Rewinds the iterator to prepare for iteration.
     *
     * @param  boolean  $prioritize  Flag to prioritize the queue.
     *
     * @return  void
     */
    public function rewind($prioritize = true)
    {
        if ($prioritize) {
            $this->_prioritize();
        }
        return parent::rewind();
    }

    /**
     * Prioritizes the queue.
     *
     * @return  void
     */
    protected function _prioritize(/* ... */)
    {
        if (!$this->dirty) return null;
        $tmp = array();
        $this->rewind(false);
        while($this->valid()) {
            $pri = $this->getInfo();
            if (!isset($tmp[$pri])) {
                $tmp[$pri] = array();
            }
            $tmp[$pri][] = $this->current();
            $this->next();
        }
        ksort($tmp, SORT_NUMERIC);
        $this->flush($this);
        foreach ($tmp as $priority => $_array) {
            foreach ($_array as $_sub) {
                parent::attach($_sub, $priority);
            }
        }
        $this->dirty = false;
    }

    public function attach($object, $data = null)
    {
        throw new \Exception('attach method disallowed; use of enqueue required');
    }

    public function detach($object)
    {
        throw new \Exception('detach method disallowed; use of dequeue required');
    }

    /**
     * Flushes the queue.
     *
     * @return  void
     */
    public function flush(/* ... */)
    {
        $this->removeAll($this);
    }
}