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
 * Engine will throw exceptions rather than signals on errors.
 */
if (!defined('ENGINE_EXCEPTIONS')) {
    define('ENGINE_EXCEPTIONS', true);
}

/**
 * Maintain the event history
 */
if (!defined('ENGINE_EVENT_HISTORY')) {
    define('ENGINE_EVENT_HISTORY', true);
}

/**
 * Allow the engine to detect inifinite looping.
 */
if (!defined('ENGINE_RECURSIVE_DETECTION')) {
    define('ENGINE_RECURSIVE_DETECTION', false);
}

/**
 * Complex signal return to trigger the signal during routine calculation.
 */
define('ENGINE_ROUTINE_SIGNAL', -0xF14E);

/**
 * As of v0.3.0 the loop is now run in respect to the currently available handles,
 * this prevents the engine from running contionusly forever when there isn't anything
 * that it needs to do.
 *
 * To achieve this the engine uses routines for calculating when to run and 
 * shutdowns when no more are available.
 *
 * The Engine uses the State and Storage traits, and will also attempt to
 * gracefully handle exceptions when ENGINE_EXCEPTIONS is turned off.
 * 
 * The queue storage has also been improved in 0.3.0, previously the storage used
 * a non-index and index based storage, the storage now uses only a single array.
 * 
 * The major improvement is the storage uses a binary search algorithm or a 
 * hash table for locating the queues, the algorithm works with 
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
     * Storage container node indices
     */
    const HASH_STORAGE = 0;
    const COMPLEX_STORAGE = 1;

    /**
     * Allows for 
     */

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
    protected $_event_history = [];

    /**
     * Current event in execution
     * 
     * @var  object  \prggmr\Event
     */
    protected $_current_event = null;

    /**
     * Number of recursive event calls
     * 
     * @var  integer
     */
    protected $_event_recursive = 0;

    /**
     * Event children
     * 
     * @var  array
     */
    protected $_event_children = [];

    /**
     * Routine data.
     * 
     * @var  array
     */
    private $_routines = [];

    /**
     * Libraries loaded
     */
    protected $_libraries = [];

    /**
     * Starts the engine.
     * 
     * @return  void
     */
    public function __construct()
    {
        $this->flush();
        if (ENGINE_EXCEPTIONS) {
            if (!class_exists('\prggmr\signal\integer\Range', false)){
                require_once 'signal/integer/range.php';
            }
            $this->handle(function(){
                $args = func_get_args();
                $type = end($args);
                $message = null;
                if ($args[0] instanceof \Exception) {
                    $message = $args[0]->getMessage();
                } else {
                    $message = engine_code($type);
                }
                throw new EngineException($message, $type, $args);
            }, new \prggmr\signal\integer\Range(0xE002, 0xE014), 0, null);
        }
    }

    /**
     * Start the event loop.
     * 
     * @param  null|integer  $ttr  Number of milliseconds to run the loop.
     * 
     * @return  void
     */
    public function loop($ttr = null)
    {
        if (null !== $ttr) {
            $this->handle(function($engine){
                $engine->shutdown();
            }, new \prggmr\signal\time\Timeout($ttr, $this));
        }
        $this->signal(esig::LOOP_START);
        while($this->_routines()) {
            // check state
            if ($this->get_state() === STATE_HALTED) break;
            
            // directly execute given sig handlers
            if (count($this->_routines[2]) != 0) {
                foreach ($this->_routines[2] as $_node) {
                    $this->_execute(
                        $_node[0], $_node[1], $this->_event($_node[0], $_node[2]), $_node[0]->vars()
                    );
                }
            }

            // signal the given signals
            if (count($this->_routines[1]) != 0) {
                foreach ($this->_routines[1] as $_signals) {
                    foreach ($_signals as $_node) {
                        $this->signal($_node[0], $_node[1], $_node[2]);
                    }
                }
            }

            // check for idle time
            if ($this->_routines[0] > 0) {
                // idle for the given time in milliseconds
                usleep($this->_routines[0] * 1000);
            }
        }
        $this->signal(esig::LOOP_SHUTDOWN);
    }

    /**
     * Runs complex signal routines for engine loop.
     * 
     * @return  boolean|array
     */
    private function _routines()
    {
        $return = false;
        $this->_routines = [0, [], []];
        // allow for external shutdown signal before running anything
        if ($this->get_state() === STATE_HALTED) return false;
        foreach ($this->_storage[self::COMPLEX_STORAGE] as $_key => $_node) {
            $routine = $_node[0]->routine($this->_event_history);
            if (is_array($routine) && count($routine) == 2) {
                // Check for signals
                if (null !== $routine[0]) {
                    if (is_array($routine[0])) {
                        foreach ($routine as $_sig) {
                            if (false === $this->_routine_exhausted($_sig)) {
                                $return = true;
                                if (!isset($_node[2])) {
                                    if (null !== $_node[0]->event()) {
                                        $_node[2] = $_node[0]->event();
                                        // destroy reference
                                        $_node[0]->event(false);
                                    } else {
                                        $_node[2] = null;
                                    }
                                }
                                $this->_routines[1][] = [$_sig, $_node[0]->vars(), $_node[2]];
                            }
                        }
                    } else {
                        // Trigger the signal itself
                        if ($routine[0] === ENGINE_ROUTINE_SIGNAL) {
                            if (false === $this->_routine_exhausted($_node[1])) {
                                $return = true;
                                // Recurring signals will always get the same event
                                if (!isset($_node[2])) {
                                    if (null !== $_node[0]->event()) {
                                        $_node[2] = $_node[0]->event();
                                        // destroy reference
                                        $this->_storage[self::COMPLEX_STORAGE][$_key][0]->event(false);
                                    } else {
                                        $_node[2] = new Event();
                                    }
                                    $this->_storage[self::COMPLEX_STORAGE][$_key][2] = $_node[2];
                                }
                                $this->_routines[2][] = $_node;
                            }
                        // Trigger one signal
                        } else {
                            if (false === $this->_routine_exhausted($_routine[0])) {
                                $return = true;
                                $this->_routines[1][] = [$_routine[0], $_node[0]->vars(), $_node[0]->event()];
                            }
                        }
                    }
                }
                // check for idle
                if ($routine[1] !== null) {
                    if ($this->_routines[0] === 0 || $this->_routines[0] > $routine[1]) {
                        $return = true;
                        $this->_routines[0] = $routine[1];
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Determines if the given signal queue has exhausted during routine calculation.
     * 
     * @param  string|integer|object  $queue
     * 
     * @return  boolean
     */
    private function _routine_exhausted($queue)
    {
        if (!$queue instanceof Queue) {
            $queue = $this->signal_queue($queue, false);
            if (false === $queue || $queue[0] === self::QUEUE_NEW) return false;
            $queue = $queue[1];
        }
        if (true === $this->queue_exhausted($queue)) {
            $this->signal(esig::EXHAUSTED_QUEUE_SIGNALED, array(
                $queue
            ));
            return true;
        }
        return false;
    }

    /**
     * Analysis a queue to determine if all handles are exhausted.
     * 
     * @param  object  $queue  \prggmr\Queue
     * 
     * @return  boolean
     */
    public function queue_exhausted($queue)
    {
        $queue->reset();
        while($queue->valid()) {
            // if a non exhausted queue is found return false
            if (!$queue->current()[0]->is_exhausted()) {
                return false;
            }
            $queue->next();
        }
        return true;
    }

    /**
     * Removes a signal handler.
     *
     * @param  mixed  $signal  Signal instance or signal.
     * @param  mixed  $handle  Handle instance or identifier.
     * 
     * @return  void
     */
    public function handle_remove($handle, $signal)
    {
        $slot = $this->signal_queue($signal);
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
        $this->_storage = [[], []];
        $this->set_state(STATE_DECLARED);
    }

    /**
     * Creates a new signal handler.
     *
     * @param  object  $callable  Closure
     * @param  string|int|object  $signal  Signal to attach the handle.
     * @param  integer $priority  Handle priority.
     * @param  integer  $exhaust  Handle exhaustion.
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
            if (!$callable instanceof \Closure) {
                $this->signal(esig::INVALID_HANDLE, array(
                    func_get_args()
                ));
                return false;
            }
            $handle = new Handle($callable, $exhaust);
        }

        $slot = $this->signal_queue($signal);
        if (false !== $slot && $slot[1] instanceof \prggmr\Queue) {
            $slot[1]->enqueue($handle, $priority);
        }

        return $handle;
    }

    /**
     * Locates or creates a signal Queue in storage.
     * 
     * @param  string|integer|object  $signal  Signal
     * @param  boolean  $create  Create the queue if not found.
     * @param  integer  $type  [QUEUE_MIN_HEAP,QUEUE_MAX_HEAP]
     *
     * @return  boolean|array  False|[QUEUE_NEW|QUEUE_EMPTY|QUEUE_NONEMPTY, queue, signal]
     */
    public function signal_queue($signal, $create = true, $type = QUEUE_MIN_HEAP)
    {
        $complex = false;
        $queue = false;
        if ($signal instanceof \prggmr\Signal\Standard) {
            if ($signal instanceof \prggmr\signal\Complex) {
                $complex = true;
            }
        } else {
            try {
                $signal = new Signal($signal);
            } catch (\InvalidArgumentException $e) {
                $this->signal(esig::INVALID_SIGNAL, array($exception, $signal));
                return false;
            }
        }

        if ($complex) {
            $search = $this->_search_complex($signal);
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
            if (!$create) {
                return false;
            }
            $queue = new Queue($type);
            if (!$complex) {
                $this->_storage[self::HASH_STORAGE][(string) $signal->info()] = [
                    $signal, $queue
                ];
            } else {
                $this->_storage[self::COMPLEX_STORAGE][] = [$signal, $queue];
            }
            $return = [self::QUEUE_NEW, $queue, $signal];
        } else {
            if ($queue->count() === 0) {
                $return = [self::QUEUE_EMPTY, $queue, $signal];
            } else {
                $return = [self::QUEUE_NONEMPTY, $queue, $signal];
            }
        }
        $this->_last_sig_added = $signal;
        return $return;
    }

    /**
     * Registers a new sig handle loader which recursively loads files in the
     * given directory when a signal is triggered.
     * 
     * @param  integer|string|object  $signal  Signal to register with
     * @param  string  $directory  Directory to load handles from
     * 
     * @return  object|boolean  \prggmr\Handle|False on error
     */
    public function handle_loader($signal, $directory, $heap = QUEUE_MIN_HEAP)
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            $this->signal(esig::INVALID_HANDLE_DIRECTORY, array(
                $directory, $signal
            ));
        }
        if (!is_string($signal) && !is_int($signal)) {
            $this->signal(esig::INVALID_SIGNAL, array($signal));
            return false;
        }
        // ensure handle always has the highest priority
        $priority = 0;
        if ($heap === QUEUE_MAX_HEAP) {
            $priority = PHP_INT_MAX;
        }
        $engine = $this;
        $handle = $this->handle(function() use ($directory, $engine) {
            $dir = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory)
                ), '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH
            );
            foreach ($dir as $_file) {
                array_map(function($i){
                    require_once $i;
                }, $_file);
            }
            // Resignal this signal
            // The current event is not passed so the handles will get a clean
            // event.
            // Event analysis will show the handles were loaded from here.
            $engine->signal($this->get_signal(), func_get_args());
            return true;
        }, $signal, 0, 1);
        return $handle;
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
        $signal = (string) $signal;
        if (isset($this->_storage[self::HASH_STORAGE][$signal])) {
            return [self::SEARCH_FOUND, $this->_storage[self::HASH_STORAGE][$signal][1]];
        }
        return [self::SEARCH_NULL, null];
    }

    /**
     * Searches for a complex signal. If given a complex signal object
     * it will attempt to locate the signal, otherwise it will evaluate the
     * signals.
     * 
     * @param  string|int|object  $signal  Signal(s) to lookup.
     * 
     * @return  array  [SEARCH_NULL|SEARCH_FOUND|SEARCH_NOOP, object|array|null]
     */
    public function _search_complex($signal)
    {
        $locate = false;
        $found = array();
        if (is_string($signal) || is_int($signal)) {
            $locate = true;
        } elseif (!$signal instanceof \prggmr\signal\Complex) {
            $this->signal(esig::INVALID_SIGNAL, array($signal));
            return [self::SEARCH_NOOP, null];
        }
        if (count($this->_storage[self::COMPLEX_STORAGE]) == 0) {
            return [self::SEARCH_NOOP, null];
        }
        foreach ($this->_storage[self::COMPLEX_STORAGE] as $_key => $_node) {
            if ($locate) {
                $eval = $_node[0]->evaluate($signal);
                if ($eval !== false) {
                    $found[] = [$_node[1], $eval];
                }
            } else {
                if ($_node[0] === $signal) {
                    return [self::SEARCH_FOUND, $this->current()[1]];
                }
            }
        }
        if ($locate && count($found) !== 0) {
            return [self::SEARCH_FOUND, $found];
        }
        return [self::SEARCH_NULL, null];
    }

    /**
     * Loads an event for the current signal.
     * 
     * @param  int|string|object  $signal
     * @param  object  $event  \prggmr\Event
     * 
     * @return  object  \prggmr\Event
     */
    private function _event($signal, $event = null)
    {
        // event creation
        if (!$event instanceof Event) {
            if (null !== $event) {
                $this->signal(esig::INVALID_EVENT, array($event));
            }
            $event = new Event();
        } else {
            if ($event->get_state() !== STATE_DECLARED) {
                $event->set_state(STATE_RECYCLED);
            }
        }
        $event->set_signal($signal);
        // are we keeping the history
        if (!ENGINE_EVENT_HISTORY) {
            return $event;
        }
        // TODO : infinite loop detection algorithm
        // if possible ... or needed
        // event history management
        if (null !== $this->_current_event) {
            $this->_event_children[] = $this->_current_event;
            $event->set_parent($this->_current_event);
        }
        $this->_current_event = $event;
        $this->_event_history[] = [$event, $signal, milliseconds()];
        return $event;
    }

    /**
     * Exits the event from the engine.
     * 
     * @param  object  $event  \prggmr\Event
     */
    private function _event_exit($event)
    {
        // event execution finished cleanup and reset current
        $event->set_state(STATE_EXITED);
        // are we keeping the history
        if (!ENGINE_EVENT_HISTORY) {
            return null;
        }
        if (count($this->_event_children) !== 0) {
            $this->_current_event = array_pop($this->_event_children);
        } else {
            $this->_current_event = null;
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
     * @return  object  Event
     */
    public function signal($signal, $vars = null, $event = null)
    {
        // check variables
        if (null !== $vars) {
            if (!is_array($vars)) {
                $vars = array($vars);
            }
        }

        // load engine event
        $event = $this->_event($signal, $event);

        // locate sig handlers
        $queue = new Queue();
        $stack = $this->_search($signal);
        if ($stack[0] === self::SEARCH_FOUND) {
            $storage = $stack[1]->storage();
            $queue->merge($storage);
        }
        $complex = $this->_search_complex($signal);
        if ($complex[0] === self::SEARCH_FOUND) {
            array_walk($complex[1], function($node) use ($queue){
                if (is_bool($node[1]) === false) {
                    $data = $node[1];
                    $node[0]->walk(function($handle) use ($data){
                        $handle[0]->params($data);
                    });
                }
                $storage = $node[0]->storage();
                $queue->merge($storage);
            });
        }

        // no sig handlers found
        if ($queue->count() === 0) {
            $this->_event_exit($event);
            return $event;
        }

        return $this->_execute($signal, $queue, $event, $vars);
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
    protected function _execute($signal, $queue, $event, $vars)
    {
        // execute sig handlers
        $queue->sort(true);
        $queue->reset();
        while($queue->valid()) {
            if ($event->get_state() === STATE_HALTED) {
                break;
            }
            $handle = $queue->current()[0];
            $handle->set_state(STATE_RUNNING);
            // bind event to allow use of "this"
            $handle->bind($event);
            // set event as running
            $event->set_state(STATE_RUNNING);
            if (ENGINE_EXCEPTIONS) {
                $result = $handle($vars);
            } else {
                try {
                    $result = $handle($vars);
                } catch (\Exception $exception) {
                    $event->set_state(STATE_ERROR);
                    $handle->set_state(STATE_ERROR);
                    if ($exception instanceof EngineException) {
                        throw $exception;
                    }
                    $this->signal(esig::HANDLE_EXCEPTION, array(
                        $exception, $signal
                    ));
                }
            }
            if (null !== $result) {
                $event->set_result($result);
                if (false === $result) {
                    $event->halt();
                }
            }
            $handle->set_state(STATE_EXITED);
            $queue->next();
        }
        $this->_event_exit($event);
        return $event;
    }

    /**
     * Retrieves the event history.
     * 
     * @return  array
     */
    public function event_history(/* ... */)
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
        $this->set_state(STATE_HALTED);
    }

    /**
     * Provides a tree structure for analyzing the system event architecture.
     * 
     * TODO
     * 
     * @return  string
     */
    public function event_analysis()
    {
        if (!ENGINE_EVENT_HISTORY) return false;
    }

    /**
     * Loads a complex signal library.
     * 
     * @param  string  $name  Signal library name.
     * @param  string|null  $dir  Location of the library. 
     * 
     * @return  void
     */
    public function load_signal($name, $dir = null) 
    {
        // already loaded
        if (isset($this->_libraries[$name])) return true;
        if ($dir === null) {
            $dir = dirname(realpath(__FILE__)).'/signal';
        } else {
            if (!is_dir($dir)) {
                $this->signal(esig::INVALID_SIGNAL_DIRECTORY, $dir);
            }
        }

        if (is_dir($dir.'/'.$name)) {
            $path = $dir.'/'.$name;
            if (file_exists($path.'/__autoload.php')) {
                // keep history of what has been loaded
                $this->_libraries[$name] = true;
                require_once $path.'/__autoload.php';
            } else {
                $this->signal(esig::SIGNAL_LOAD_FAILURE, [$name, $dir]);
            }
        }
    }
}

class EngineException extends \Exception {

    protected $_type = null;

    protected $_args = null;

    /**
     * Constructs a new engine exception.
     * 
     * @param  string|null  $message  Exception message if given
     * @param  integer  $type  Engine error type
     * @param  array  $args  Arguments present for exception
     */
    public function __construct($message, $type, $args)
    {
        parent::__construct($message);
        $this->_type = $type;
        $this->_args = $args;
    }

    /**
     * Returns exception arguments.
     * 
     * @return  array
     */
    public function get_args(/* ... */)
    {
        return $this->_args;
    }

    /**
     * Returns engine exception code.
     * 
     * @return  integer
     */
    public function get_engine_code(/* ... */)
    {
        return $this->_type;
    }
}