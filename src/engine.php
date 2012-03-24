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
 * Engine Hash table
 */
define('ENGINE_HASH_STORAGE', 1);
/**
 * Engine binary storage
 */
define('ENGINE_BINARY_STORAGE', 2);

if (defined('ENGINE_USE_BINARY')) {
    define('ENGINE_STORAGE_TYPE', ENGINE_BINARY_STORAGE);
} else {
    define('ENGINE_STORAGE_TYPE', ENGINE_HASH_STORAGE);
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
 * The major improvement is the storage uses a binary search algorithm or a 
 * direct index lookup for locating the queues, the algorithm works with 
 * strings, integers and \prggmr\signal\Complex objects providing a major 
 * performance increase over the previous implementation.
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
     * Last sig handler added to the engine.
     * 
     * @var  object
     */
    protected $_last_sig_added = null;

    /**
     * History of events
     * 
     * @var  array
     */
    protected $_event_history = array();

    /**
     * Current event in execution
     * 
     * @var  object  \prggmr\Event
     */
    protected $_current_event = null;

    /**
     * Event children
     * 
     * @var  array
     */
    protected $_event_children = array();

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
     * Empties the storage, history and clears the current state.
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
    public function handle($callable, $signal, $priority = QUEUE_DEFAULT_PRIORITY, $exhaust = 1)
    {
        if (is_int($signal) && $signal >= 0xE001 && $signal <= 0xE02A) {
            $this->signal(esig::RESTRICTED_SIGNAL, array(
                func_get_args()
            ));
        }

        if (!$callable instanceof Handle) {
            if (!is_callable($callable)) {
                $this->signal(esig::INVALID_HANDLE, array(
                    func_get_args()
                ));
                return false;
            }
            $handle = new Handle($callable, $exhaust);
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
                $this->signal(esig::INVALID_SIGNAL, array($signal));
                return false;
            }
        }

        if ($complex) {
            $search = $this->_searchComplex($signal);
            if ($search[0] === self::SEARCH_FOUND) {
                $queue = $search[1];
            }
        } else {
            $search = $this->_search($signal);
            if ($search[0] === self::SEARCH_FOUND) {
                $queue = $search[1];
            }
        }

        if (!$queue) {
            $queue = new Queue($type);
            if (ENGINE_STORAGE_TYPE == ENGINE_HASH_STORAGE && !$complex) {
                $this->_storage[(string) $signal->info()] = [
                    $signal, $queue
                ];
                if ($this->_last_sig_added instanceof \prggmr\Signal\Complex) {
                    $this->_unsorted = true;
                }
            } else {
                $this->_storage[] = [$signal, $queue];
                $this->_unsorted = true;
            }
            $return = [self::QUEUE_NEW, $queue, $signal];
        } else {
            if ($queue->count() === 0) {
                $return = [self::QUEUE_EMPTY, $queue, $signal];
            }
            $return = [self::QUEUE_NONEMPTY, $queue, $signal];
        }

        $this->_last_sig_added = $signal;
        return $return;
    }

    /**
     * Sorts the storage.
     * 
     * @return  void
     */
    protected function _sort() 
    {
        if (!$this->_unsorted) return null;
        $cmp = function($_node1, $_node2){
            if ($_node1[0] instanceof \prggmr\signal\Complex) {
                return 1;
            }
            if ($_node2[0] instanceof \prggmr\signal\Complex) {
                return -1;
            }
            if (ENGINE_STORAGE_TYPE == ENGINE_HASH_STORAGE) return 0;
            $_node1 = $_node1[0]->info();
            $_node2 = $_node2[0]->info();
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
        };

        if (ENGINE_STORAGE_TYPE == ENGINE_HASH_STORAGE) {
            $this->uasort($cmp);
        } else {
            $this->usort($cmp);
        }

        $this->_unsorted = false;
    }

    /**
     * Searches for a string or integer signal queue in storage.
     * 
     * @param  string|int|object  $signal  Signal for queue
     * 
     * @return  array  [SEARCH_NULL|SEARCH_FOUND|SEARCH_NOOP, object|null]
     */
    protected function _search($signal) 
    {
        if ($signal instanceof \prggmr\signal\Complex) {
            return [self::SEARCH_NOOP, null];
        }
        if ($signal instanceof \prggmr\Signal) {
            $signal = $signal->info();
        }
        $this->_sort();
        if (ENGINE_STORAGE_TYPE == ENGINE_HASH_STORAGE) {
            $signal = (string) $signal;
            if (isset($this->_storage[$signal])) {
                return [self::SEARCH_FOUND, $this->_storage[$signal][1]];
            }
        } else {
            if ($this->count() >= BINARY_ENGINE_SEARCH) {
                if ($this->_unsorted) $this->_sort();
                $signal = ($signal instanceof \prggmr\Signal) ? $signal->info() : $signal;
                return bin_search($signal, $this->_storage, function($_node1, $_node2){
                    if ($_node1[0] instanceof \prggmr\signal\Complex) {
                        return 1;
                    }
                    $_node1 = $_node1[0]->info();
                    if (is_int($_node1)){
                        if (is_string($_node2)) {
                            return -1;
                        }
                        if ($_node1 > $_node2) return 1;
                        if ($_node1 < $_node2) return -1;
                        if ($_node1 == $_node2) return 0;
                    }
                    if (is_string($_node1)) {
                        if (is_int($_node2)) {
                            return 1;
                        }
                        return strcmp($_node1, $_node2);
                    }
                });
            } else {
                $this->reset();
                if ($signal instanceof Signal) {
                    $signal = $signal->info();
                }
                while ($this->valid()) {
                    if ($this->current()[0]->info() === $signal) {
                        return [self::SEARCH_FOUND, $this->current()[1]];
                    }
                    $this->next();
                }
            }
        }
        return [self::SEARCH_NULL, null];
    }

    /**
     * Searches for a complex signal. If given a complex signal object
     * it will attempt to locate the signal, otherwise it will attempt to locate
     * any signal handlers. 
     * 
     * @param  string|int|object  $signal  Signal(s) to lookup.
     * 
     * @return  array  [SEARCH_NULL|SEARCH_FOUND|SEARCH_NOOP, object|array|null]
     */
    public function _searchComplex($signal)
    {
        if (is_string($signal) || is_int($signal)) {
            $locate = true;
            $found = array();
        } elseif (!$signal instanceof \prggmr\signal\Complex) {
            $this->signal(esig::INVALID_SIGNAL, array($signal));
            return [self::SEARCH_NOOP, null];
        }
        $this->_sort();
        $this->end();
        while ($this->valid()) {
            if (!$this->current()[0] instanceof \prggmr\signal\Complex) {
                // stop looking no longer in complex storage
                break;
            }
            if ($locate) {
                $eval = $this->current()[0]->evaluate($signal);
                if ($eval !== false) {
                    $found[] = [$this->current()[1], $eval];
                }
            } else {
                if ($this->current()[0] === $signal) {
                    return [self::SEARCH_FOUND, $signal];
                }
            }
            $this->prev();
        }

        if ($locate && count($found) !== 0) {
            return [self::SEARCH_FOUND, $found];
        }
        return [self::SEARCH_NULL, null];
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
     * @return  object|null  Event|Null if no handlers
     */
    public function signal($signal, $vars = null, &$event = null, $stacktrace = null)
    {
        if (null !== $vars) {
            if (!is_array($vars)) {
                $vars = array($vars);
            }
        }

        // event creation
        if (!$event instanceof Event) {
            if (null !== $event) {
                $this->signal(esigs::INVALID_EVENT, array($event));
            }
            $event = new Event();
            $event->setState(STATE_RUNNING);
        } else {
            if ($event->getState() !== STATE_DECLARED) {
                $event->setState(STATE_RECYCLED);
            }
        }

        // event history management
        $event->addSignal($signal);
        if (null !== $this->_current_event) {
            $this->_event_children[] = $this->_current_event;
            $event->setParent($this->_current_event);
        }
        $this->_current_event = $event;
        $this->_event_history[] = $event;

        // locate sig handlers
        $queue = new Queue();
        $stack = $this->_search($signal);
        if ($stack[0] === self::SEARCH_FOUND) {
            $queue->merge($stack[1]->storage());
        }
        $complex = $this->_searchComplex($signal);
        if ($complex[0] === self::SEARCH_FOUND) {
            array_walk($complex[1], function($node) use ($queue){
                if (is_bool($node[1]) === false) {
                    $data = $node[1];
                    $node[0]->walk(function($handle) use ($data){
                        $handle->params($data);
                    });
                }
                $queue->merge($node[0]->storage());
            });
        }

        // no sig handlers found
        if ($queue->count() === 0) return null;

        // execute sig handlers
        $queue->sort(true);
        $queue->reset();
        while($queue->valid()) {
            if ($event->getState() === STATE_HALTED) break;
            $this->_execute($signal, $queue->current()[0], $event, $vars);
            $queue->next();
        }

        // event execution finished cleanup and reset current
        $event->setState(STATE_EXITED);
        if (count($this->_event_children) !== 0) {
            $this->_current_event = array_pop($this->_event_children);
        } else {
            $this->_current_event = null;
        }
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
    protected function _execute($signal, &$handle, &$event, &$vars)
    {
        $handle->setState(STATE_RUNNING);
        // bind event to allow use of "this"
        $handle->bind($event);
        try {
            $result = $handle->execute($vars);
        } catch (\prggmr\HandleException $exception) {
            $event->setState(STATE_ERROR);
            $handle->setState(STATE_ERROR);
            $this->signal(esig::HANDLE_EXCEPTION, array(
                $exception, $signal
            ));
        }
        if (null !== $result) {
            $event->setResult($result);
            if (false === $result) {
                $event->halt();
            }
        }
        $handle->setState(STATE_EXITED);
        return $event;
    }

    /**
     * Retrieves the event history.
     * 
     * @return  array
     */
    public function history(/* ... */)
    {
        return $this->_event_history;
    }

    /**
     * Sends the engine the shutdown signal.
     *
     * @return  void
     */
    public function shutdown()
    {
        $this->setState(STATE_HALTED);
    }
}