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


use \Closure,
    \InvalidArgumentException;

/**
 * As of v0.3.0 the loop is now run in respect to the currently available handles,
 * this prevents the engine from running contionusly forever when there isn't anything
 * that it needs to do.
 *
 * To achieve this the engine uses routines for calculating when to run,
 * the default routines are based on time which calculates the time an event is
 * to run and sleeps the engine until, the other processes the available signals
 * and shutdowns the engine when no more are available for running.
 *
 * The Engine now uses the State trait.
 */
class Engine {

    /**
     * Statefull object
     */
    use State, Storage;

    /**
     * Engine stacktrace.
     *
     * @var  array
     */
    protected $_stacktrace = array();

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
     * Clears a set timeout.
     *
     * @param  mixed  $handler  Handler instance of identifier.
     *
     * @return  boolean
     */
    public function clearTimeout($handler)
    {
        $this->clearInterval($handler);
    }

    /**
     * Returns the count of signals in the engine.
     *
     * @return  integer
     */
    public function countSignals()
    {
        return count($this->_non_index_storage) + count($this->_index_storage);
    }

    /**
     * Returns the number of timers in the engine.
     *
     * @return integer
     */
    public function countTimers()
    {
        return count($this->_timers);
    }

    /**
    * Remove a handle from the queue.
    *
    * @param  mixed  $signal  Signal instance or signal.
    *
    * @param  mixed  $handle  Handle instance or identifier.
    *
    * @throws  InvalidArgumentException
    * @return  void
    */
    public function dequeue($signal, $handle)
    {
        $queue = $this->queue($signal, false);
        if (false === $queue) return false;
        return $queue->dequeue($handle);
    }

    /**
     * Flushes the engine and resets its state.
     *
     * @return void
     */
    public function flush(/* ... */)
    {
        $this->_storage->setSize(0);
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
     * Attaches a handle to a signal.
     *
     * @param  mixed  $callable  Function to execute on handle.
     *
     * @param  mixed  $signal  Signal which triggers the handle.
     *
     * @param  string  $identifier  Identifier of handle.
     *
     * @param  integer $priority  Handle priority.
     *
     * @param  mixed  $chain  Signal to chain after handle execution.
     *
     * @param  integer  $exhaust  Rate at which handle exhausts. DEFAULT = 1
     *
     * @throws  InvalidArgumentException  Thrown when an invalid callback is
     *          provided.
     *
     * @return  object  Subscription
     */
    public function handle($callable, $signal, $identifier = null, $priority = null, $chain = null, $exhaust = 1)
    {
        if (!$callable instanceof Handle) {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException(
                    'callable is not a valid php callback'
                );
            }
            $handle = new Handle($callable, $identifier, $exhaust);
        }

        $queue = $this->queue($signal);
        $queue->enqueue($handle, $priority);

        if (null !== $chain) {
            $queue->getSignal()->setChain($chain);
        }

        return $handle;
    }

    /**
     * Locates a Queue object in storage.
     *
     * @param  mixed  $signal  Signal instance or signal.
     * @param  boolean  $generate  Generate the queue if not found.
     *
     * @return  mixed  Boolean if generate is false. Queue instance.
     */
    public function queue($signal, $generate = true)
    {
        $obj = (is_object($signal) && $signal instanceof Signal);
        $indexable = false;
        if (static::canIndex($signal)) {
            $index = ($obj) ? $signal->signal() : $signal;
            if (isset($this->_index_storage[$index])) {
                return $this->_index_storage[$index];
            }
            $indexable = true;
        } else {
            $length = count($this->_non_index_storage);
            for($i=0;$i!=$length;$i++) {
                if (($obj && $this->_non_index_storage[$i]->getSignal() === $signal) ||
                ($this->_non_index_storage[$i]->getSignal(true) === $signal)) {
                    return $this->_non_index_storage[$i];
                }
            }
        }

        if (!$generate) return false;

        if (!(is_object($signal) && $signal instanceof Signal)) {
            $signal = new Signal($signal);
        }

        $queue = new Queue($signal);

        if ($indexable) {
            $this->_index_storage[$index] = $queue;
        } else {
            $this->_non_index_storage[] = $queue;
        }
        return $queue;
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

    /**
     * Calls a function after the specified amount of time in microseconds.
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
    public function setTimeout($callable, $interval, $vars = null, $identifier = null, $start = null)
    {
        // This simply uses set interval and sets an exhaustion rate of 1 ...
        return $this->setInterval($callable, $interval, $vars, $identifier, 1, $start);
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
}