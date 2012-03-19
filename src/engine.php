<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use \Closure,
    \InvalidArgumentException,
    \prggmr\engine\Signals as esig;

/**
 * When to begin binary searching.
 */
if (!defined('BINARY_ENGINE_SEARCH')) {
    define('BINARY_ENGINE_SEARCH', 75);
}

/**
 * As of v0.3.0 the loop is now run in respect to the currently available handles,
 * this prevents the engine from running contionusly forever when there isn't anything
 * that it needs to do.
 *
 * To achieve this the engine uses routines for calculating when to run,
 * the default routines are based on time which calculates the time a handle is
 * to run and sleeps until then, the other processes the available handles
 * and shutdowns the engine when no more are available.
 *
 * The Engine uses the State and Storage traits, and will also attempt to
 * gracefully handle exceptions.
 * 
 * The queue storage has also been improved in 0.3.0, previously the storage used
 * a non-index and index based storage, the storage now uses only a single array.
 * 
 * The major improvement is the storage uses a binary search algorithm for
 * locating the queues, the algorithm works with strings, integers and 
 * \prggmr\signal\Complex objects providing a major performance increase over
 * the previous implementation.
 */
class Engine {

    /**
     * Statefull object
     */
    use State, Storage;

    /**
     * Returns of calling queue.
     * 
     * QUEUE_NEW
     * A new empty queue was created.
     * 
     * QUEUE_EMPTY
     * An empty queue was found.
     * 
     * QUEUE_NONEMPTY
     * A non-empty queue was found.
     */
    const QUEUE_NEW = 0xA01;
    const QUEUE_EMPTY = 0xA02;
    const QUEUE_NONEMPTY = 0xA03;

    /**
     * Search Results
     * 
     * SEARCH_NULL
     * Found no results
     * 
     * SEARCH_FOUND
     * Found a single result
     * 
     * SEARCH_NOOP
     * Search is non-operational (looking for non-searchable)
     */
    const SEARCH_NULL = 0xA04;
    const SEARCH_FOUND = 0xA05;
    const SEARCH_NOOP = 0xA06;

    /**
     * Allows for 
     */

    /**
     * Current engine stacktrace, this is keep for when a handle errors out
     * and php does not maintain the actual trace to the call.
     *
     * @var  array
     */
    protected $_stacktrace = array();

    /**
     * Determins if queue storage needs to be sorted.
     * 
     * @var  boolean
     */
    protected $_unsorted = false;


    /**
    * Removes a signal handler.
    *
    * @param  mixed  $signal  Signal instance or signal.
    *
    * @param  mixed  $handle  Handle instance or identifier.
    *
    * @throws  InvalidArgumentException
    * 
    * @return  void
    */
    public function dehandle($signal, $handle)
    {
        $slot = $this->queue($signal);
        if ($slot[0] <= self::QUEUE_EMPTY) return false;
        return $slot[1]->dequeue($handle);
    }

    /**
     * Empties the storage and clears the current state.
     *
     * @return void
     */
    public function flush(/* ... */)
    {
        $this->_storage = [];
        $this->setState(STATE_DECLARED);
    }

    /**
     * Starts the event loop.
     *
     * @param  boolean  $reset  Resets all timers to begin at loop start.
     * @param  integer  $timeout  Number of milliseconds to run the loop.
     *
     * @return  void
     */
    public function loop($reset = false, $timeout = 100)
    {
        # Reset all timers
        if ($reset) {
            $this->handle(self::INTERVAL_RESET);
            foreach($this->_timers as $_index => $_timer) {
                $this->_timers[$_index][2] = $this->getMilliseconds() + $this->_timers[$_index][1];
            }
        }
        // this can be cleared using clearTime(Engine::LOOP_SHUTDOWN_TIMEOUT)
        // but after that the loop will run indefinitly
        if (null !== $timeout && is_int($timeout)) {
            // this is a required hack ... i know
            // php 5.4 will hopefully provide a fix
            $engine = $this;
            $this->setTimeout(function() use ($engine) {
                $engine->shutdown();
            }, $timeout, null, Engine::LOOP_SHUTDOWN_TIMEOUT);
        }
        # Run routine calculations
        while($this->_routines()) {
            # Signal shutdown based on the state
            $engine_state = $this->getState();
            if (static::SHUTDOWN === $engine_state ||
                static::ERROR === $engine_state) {
            }
            $this->_handleTimers();
            foreach($this->_timers as $_index => $_timer) {
                if (!isset($this->_timers[$_index])) {
                    continue;
                }
                if ($this->getMilliseconds() >= $_timer[2]) {
                    $vars = $_timer[3];
                    if (null !== $vars) {
                        if (!is_array($vars)) {
                            $vars = array($vars);
                        } else {
                            if (isset($vars[0]) && !$vars[0] instanceof Event) {
                                array_unshift($vars, new Event());
                            }
                        }
                    } else {
                        $vars = array(new Event());
                    }
                    if (!$vars[0] instanceof Event) {
                        array_unshift($vars, new Event());
                    }
                    if (!$vars[0]->isHalted()){
                        $this->_execute(null, $_timer[0], $vars);
                    }
                    if (isset($this->_timers[$_index])) {
                        $this->_timers[$_index][3] = $vars;
                        $this->_timers[$_index][2] = $this->getMilliseconds() + $_timer[1];
                        if ($_timer[0]->isExhausted()) {
                            unset($this->_timers[$_index]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Attach a new handle to a signal.
     *
     * @param  mixed  $callable  Function to execute on handle.
     *
     * @param  mixed  $signal  Signal which triggers the handle.
     *
     * @param  string  $identifier  Identifier of handle.
     *
     * @param  integer $priority  Handle priority.
     *
     * @param  integer  $exhaust  Rate at which handle exhausts. DEFAULT = 1
     *
     * @return  object|boolean  Handle, boolean if error
     */
    public function handle($callable, $signal, $identifier = null, $priority = QUEUE_DEFAULT_PRIORITY, $exhaust = 1)
    {
        if (is_int($signal) && $signal >= 0xE001 && $signal <= 0xE02A) {
            $this->signal(esig\Signal::INVALID_HANDLE, array(
                func_get_args()
            ));
        }
        if (!$callable instanceof Handle) {
            if (!is_callable($callable)) {
                $this->signal(esig\Signal::INVALID_HANDLE, array(
                    func_get_args()
                ));
                return false;
            }
            $handle = new Handle($callable, $identifier, $exhaust);
        }

        $slot = $this->sigHandler($signal);
        $slot[1]->enqueue($handle, $priority);

        if (null !== $chain) {
            $slot[2]->setChain($chain);
        }

        return $handle;
    }

    /**
     * Locates or creates a signal Queue in storage.
     * 
     * The storage is designed to place any sortable types [int, strings and
     * sortable objects] at top of the stack and place any unstortable types 
     * [complex objects] at the bottom.
     * 
     * A visual representation:
     * 
     * [
     *     1,2,object(3),4
     *     'a','b',object('c'),'d'
     *     object(c2), object(c2)
     * ]
     * 
     * @param  string|integer|object  $signal  Signal
     * @param  integer  $type  [QUEUE_MIN_HEAP,QUEUE_MAX_HEAP]
     *
     * @return  array  [QUEUE_NEW|QUEUE_EMPTY|QUEUE_NONEMPTY, queue, signal]
     */
    public function sigHandler($signal, $type = QUEUE_MIN_HEAP)
    {
        $complex = false;
        $queue = false;

        if ($signal instanceof Signal) {
            if ($signal instanceof \prggmr\signal\Complex) {
                $complex = true;
            }
        } else {
            try {
                $signal = new Signal($signal);
            } catch (\InvalidArgumentException $e) {
                $this->signal()
                return false;
            }
        }

        if ($complex) {
            // start at the bottom of the stack and go in reverse
            $this->end();
            while ($this->valid()) {
                if ($this->current()[0] === $signal) {
                    $queue = $this->current();
                    break;
                }
                if (!$this->current()[0] instanceof \prggmr\signal\Complex) {
                    // stop looking no longer in complex storage
                    break;
                }
                $this->prev();
            }
        } else {
            $index = $this->_search($signal);
            if (null !== $index) {
                $queue = $this->_storage[$index][1]; 
            } else {
                $this->_unsorted = true;
            }
        }

        if (!$queue) {
            $queue = new Queue($type);
            $this->_storage[] = [$signal, $queue];
            if (!$signal instanceof \prggmr\signal\Complex) {
                $this->_unsorted = true;
            }
            return [self::QUEUE_NEW, $queue, $signal];
        } else {
            if ($queue->count() === 0) {
                return [self::QUEUE_EMPTY, $queue, $signal];
            }
            return [self::QUEUE_NONEMPTY, $queue, $signal];
        }
    }

    /**
     * Sorts the storage.
     * 
     * @return  void
     */
    public function _sort(/* ... */) 
    {
        if (!$this->_unsorted) return null;

        $this->usort(function($_node1, $_node2){
            if ($_node1[0] instanceof \prggmr\signal\Complex) {
                return 1;
            }
            if ($_node2[0] instanceof \prggmr\signal\Complex) {
                return -1;
            }
            $_node1 = $_node1[0]->getSignal();
            $_node2 = $_node2[0]->getSignal();
            if (is_int($_node1)){
                if (is_string($_node2)) {
                    return -1;
                }
                if ($_node1 > $_node2) return 1;
                if ($_node1 < $_node2) return -1;
                if ($_node1 == $_node2) return 0;
            }
            if (is_string($_node1)) {
                if (is_int($node_2)) {
                    return 1;
                }
                return strcmp($_node1, $_node2);
            }
        });

        $this->_unsorted = false;
    }

    /**
     * Searches for a queue in storage.
     * 
     * @param  string|int|object  $signal  Signal for queue
     * 
     * @return  array  [SEARCH_NULL|SEARCH_FOUND|SEARCH_NOOP, object]
     */
    public function _search($signal) 
    {
        if ($signal instanceof \prggmr\signal\Complex) {
            return [self::SEARCH_NOOP, null];
        }
        if ($this->count() >= BINARY_ENGINE_SEARCH) {
            if ($this->_unsorted) $this->_sort();
            $signal = ($signal instanceof \prggmr\Signal) ? $signal->getSignal() : $signal;
            /**
             * Performs a binary search for the given node. 
             * This will perform much faster in larger systems where anything
             * more than a few dozen signals could be registered.
             */
            return bin_search($signal, $this->_storage, function($_node1, $_node2){
                if ($_node1[0] instanceof \prggmr\signal\Complex) {
                    return 1;
                }
                $_node1 = $_node1[0]->getSignal();
                if (is_int($_node1)){
                    if (is_string($_node2)) {
                        return -1;
                    }
                    if ($_node1 > $_node2) return 1;
                    if ($_node1 < $_node2) return -1;
                    if ($_node1 == $_node2) return 0;
                }
                if (is_string($_node1)) {
                    if (is_int($node_2)) {
                        return 1;
                    }
                    return strcmp($_node1, $_node2);
                }
            });
        } else {
            $this->reset();
            $is_object = is_object($signal);
            while ($this->valid()) {
                if ($is_object && $this->current()[0] === $signal ||
                    $this->current()[0]->getSignal() === $signal) {
                    return $this->current();
                }
                $this->next();
            }
        }
    }

    /**
     * Signals an event.
     *
     * @param  mixed  $signal  Signal instance or signal.
     *
     * @param  array  $vars  Array of variables to pass handles.
     *
     * @param  object  $event  \prggmr\Event
     *
     * @param  array  $stacktrace  Stacktrace array
     *
     * @return  object  EventGG
     */
    public function signal($signal, $vars = null, $event = null, $stacktrace = null)
    {
        if (!$signal instanceof $signal) {
            
        }
        
        // Create a temporary queue
        $queue = new Queue(new Signal($signal));

        if (null !== $vars) {
            if (!is_array($vars)) {
                $vars = array($vars);
            }
        }

        if (null === $event || !is_object($event)) {
            $event = new Event();
        } elseif (!$event instanceof Event) {
            throw new \InvalidArgumentException(
                sprintf(
                    'signal expected instance of Event received "%s"'
                , get_class($event))
            );
        }

        if (0 === count($vars)) {
            $vars = array(&$event);
        } else {
            $vars = array_merge(array(&$event), $vars);
        }

        $hashandles = false;

        // Event is now running
        $event->setState(State::RUNNING);

        // index lookup
        $obj = (is_object($signal) && $signal instanceof Signal);

        // if () {
        //     $index = ($obj) ? $signal->getSignal() : $signal;
        //     if (isset($this->_index_storage[$index])) {
        //         $_queue = $this->_index_storage[$index];
        //         // rewind only
        //         $_queue->rewind(false);
        //         while($_queue->valid()){
        //             $queue->enqueue(
        //                 $_queue->current(),
        //                 $_queue->getInfo()
        //             );
        //             $this->_index_storage[$index]->next();
        //         }
        //     }
        // }

        $length = count($this->_non_index_storage);
        if (0 !== $length) {
            for($i=0;$i!=$length;$i++) {
                if (false !==
                    ($compare = $this->_non_index_storage[$i]->getSignal()->compare($signal))) {
                    // rewind only
                    $this->_non_index_storage[$i]->rewind(false);
                    while($this->_non_index_storage[$i]->valid()){
                        $this->_non_index_storage[$i]->current()->params($compare);
                        $queue->enqueue(
                            $this->_non_index_storage[$i]->current(),
                            $this->_non_index_storage[$i]->getInfo()
                        );
                        $this->_non_index_storage[$i]->next();
                    }
                }
            }
        }

        // keep the event in an active state until everything completes
        $event->setState(Event::STATE_INACTIVE);

        // the queue is dirty
        $queue->dirty = true;

        // the queue loop
        $queue->rewind();

        // add stacktrace
        if (PRGGMR_DEBUG === true) {
            if (null === $stacktrace) {
                if (version_compare(phpversion(), '5.3.6', '>=')) {
                    $event->addTrace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
                } else {
                    $event->addTrace(debug_backtrace(false));
                }
            } else {
                $event->addTrace($stacktrace);
            }
        }

        while($queue->valid()) {
            $event->setSignal($queue->getSignal());
            if ($event->isHalted()) break;
            $this->_execute($queue->getSignal(), $queue->current(), $vars);
            if (!$event->isHalted() &&
                null !== ($chain = $queue->getSignal()->getChain())) {
                foreach ($chain as $_chain) {
                    $link = $this->signal($_chain, $vars, $event, $stacktrace);
                    if (false !== $chain) {
                        $event->setChain($link);
                    }
                }
            }
            $queue->next();
        }

        // release temporary queue
        unset($queue);
        return $event;
    }

    /**
     * Fires a handle.
     *
     * @param  object  $signal  Signal instance.
     *
     * @param  object  $handle  Handle instance.
     *
     * @param  object  $event  Event instance.
     *
     * @param  array  $vars  Array of variables to pass handles.
     *
     * @return  object  Event
     */
    protected function _execute($signal, $handle, &$event, &$vars)
    {
        $event->setHandle($handle);
        try {
            $result = $handle->execute($vars);
        } catch (\prggmr\HandleException $e) {
            $event->setState(\prggmr\Event::STATE_ERROR);
            $this->signal(engine\Signals::HANDLE_EXCEPTION, array(
                $e, $handle, $event
            ));
        }
        if (!$event instanceof \prggmr\Event) {
            throw new \RuntimeException(sprintf(
                'Event object has been replaced in handle %s',
                $subscription->getIdentifier()
            ));
        }
        if (null !== $result) {
            $event->setReturn($result);
            if (false === $result) {
                $event->halt();
            }
        }
        if ($event->getState() == Event::STATE_ERROR) {
            if ($this->getState() === Engine::LOOP) {
                if (false === $this->clearInterval($handle)) {
                    $this->dequeue($signal, $handle);
                }
            } else {
                $this->dequeue($signal, $handle);
            }
        }
        if ($handle->isExhausted()) {
            $this->dequeue($signal, $subscription);
        }
        return $event;
    }

    /**
     * Sends the engine the shutdown signal while in loop mode.
     *
     * @return  void
     */
    public function shutdown()
    {
        $this->setState(STATE_HALTED);
    }

    /**
     * Clears a set interval.
     *
     * @param  mixed  $handler  Handler instance of identifier.
     *
     * @return  boolean
     */
    public function clearInterval($handler)
    {
        $timers = count($this->_timers);
        if (is_object($handler) && $handle instanceof Handler) {
            $index = array_search($handler, $this->_timers);
            if (false === $index) return false;
        } else {
            foreach($this->_timers as $_index => $_timer) {
                if ($_timer[0]->getIdentifier() === $handler) {
                    $index = $_index;
                    break;
                }
            }
            if (!isset($index)) return false;
        }
        unset($this->_timers[$index]);
        return true;
    }

    /**
     * Calls a function at the specified intervals of time in microseconds.
     *
     * @param  mixed  $callable  Callable php variable.
     *
     * @param  integer  $interval  Interval of time in microseconds to trigger.
     *
     * @param  mixed  $vars  Variables to pass the interval.
     *
     * @param  string  $identifier  Identifier of the function.
     *
     * @param  integer  $exhaust  Rate at which this handler will exhaust.
     *
     * @param  mixed  $start  Unix parse able date to start the function interval.
     *
     * @throws  InvalidArgumentException  Thrown when an invalid callback,
     *          interval or un-parse able date is provided.
     *
     * @return  object  Handler
     */
    public function setInterval($callable, $interval, $vars = null, $identifier = null, $exhaust = 0, $start = null)
    {
        if (!$callable instanceof Handler) {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException(
                    'function callback is not valid'
                );
            }
            $handler = new Handler($callable, $identifier, $exhaust);
        }

        if (!is_int($interval)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'invalid time interval expected integer received %s',
                    gettype($interval)
                )
            );
        }
        // Allow support for setting the interval in the future
        // e.g. Set to begin at 12/1/1 at 12:00pm every 24 hours
        if (null !== $start) {
            if (is_int($start)) {
                if (time() <= $start) {
                    $timestamp = $start;
                }
            } else {
                $timestamp = strtotime($start, time());
                if (false !== $timestamp) {
                    if (time() >= $timestamp) {
                        $start = null;
                    }
                } else {
                    throw new \InvalidArgumentException(sprintf(
                        'Un-parse able date given as starting time (%s)',
                        $start
                    ));
                }
            }
        }
        if (null !== $start) {
            // really this is getting old
            $engine = $this;
            $this->setTimeout(function() use ($engine, $handler, $interval, $vars, $exhaust){
                $engine->setInterval($handler, $interval, $vars, $identifier, $exhaust, null);
            }, ($timestamp - time()) * 1000, null);
            // notice that this does seconds only not milli seconds
        } else {
            $this->_timers[] = array($subscription, $interval, $this->getMilliseconds() + $interval, $vars);
        }
        return $handler;
    }

    public function setTimeout($callable, $interval, $vars = null, $identifier = null, $start = null)
    {
        // This simply uses set interval and sets an exhaustion rate of 1 ...
        return $this->setInterval($callable, $interval, $vars, $identifier, 1, $start);
    }

}