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
    \ArrayObject,
    \InvalidArgumentException;

/**
 * As of v0.1.2 the engine uses 2 different storages, indexed and non-indexed
 * for performance. Indexable signals (integers and strings) are placed in
 * the indexed storage and allow for index based lookups, non-indexable
 * signals (objects, floats, booleans, arrays and non-indexable Signal objects)
 * are placed in the non-indexed storage and require loop through lookups.
 */
class Engine {

    /**
     * An indexed storage of Queues.
     *
     * @var  array
     */
    protected $_index_storage = null;

    /**
     * A non index storage of Queue
     *
     * @var  array
     */
    protected $_non_index_storage = null;

    /**
     * Timer based events
     *
     * @var array
     */
    protected $_timers = null;

    /**
     * Current engine state.
     *
     * @var  integer
     */
    protected $_state = null;

    /**
     * Engine states.
     */
    const RUNNING  = 0x64;
    const LOOP     = 0x65;
    const SHUTDOWN = 0x66;
    const ERROR    = 0x67;

    /**
     * Loop shutdown timeout.
     */
    const LOOP_SHUTDOWN_TIMEOUT = 'loop_shutdown';

    /**
     * Engine stacktrace.
     *
     * @var  array
     */
    protected $_stacktrace = array();

    /**
     * Construction inits our empty storage array and sets default state.
     *
     * @return  void
     */
    public function __construct(/* ... */)
    {
        $this->flush();
    }

    /**
     * Attaches a new subscription to a signal queue.
     *
     * @param  mixed  $subscription  Subscription closure that will trigger on
     *         fire or a Subscription object.
     *
     * @param  mixed  $signal  Signal the subscription will attach to, this
     *         can be a Signal object, the signal representation or an array
     *         for a chained signal.
     *
     * @param  string  $identifier  Identifier of this subscription.
     *
     * @param  integer $priority  Priority of the subscription
     *
     * @param  mixed  $chain  Chain signal
     *
     * @param  integer  $exhaust  Count to set subscription exhaustion.
     *
     * @throws  InvalidArgumentException  Thrown when an invalid callback is
     *          provided.
     *
     * @return  object  Subscription
     */
    public function subscribe($subscription, $signal, $identifier = null, $priority = null, $chain = null, $exhaust = 0)
    {
        /***
         * To note about this ... This will allow for "legacy subscribing"
         * putting the signal first, but after alot of use the function
         * should be first.
         *
         * This will be phased out or will it?
         */
        if ($signal instanceof \Closure) {
            $tmp = $subscription;
            $subscription = $signal;
            $signal = $tmp;
            // pretend this didnt happen
            unset($tmp);
        }
        if (!$subscription instanceof Subscription) {
            if (!is_callable($subscription)) {
                throw new \InvalidArgumentException(
                    'subscription callback is not a valid callback'
                );
            }
            $subscription = new Subscription($subscription, $identifier, $exhaust);
        }

        $queue = $this->queue($signal);
        $queue->enqueue($subscription, $priority);

        if (null !== $chain) {
            $queue->getSignal()->setChain($chain);
        }

        return $subscription;
    }

    /**
    * Removes a subscription from the queue.
    *
    * @param  mixed  $signal  Signal the subscription is attached to, this
    *         can be a Signal object or the signal representation.
    *
    * @param  mixed  subscription  String identifier of the subscription or
    *         a Subscription object.
    *
    * @throws  InvalidArgumentException
    * @return  void
    */
    public function dequeue($signal, $subscription)
    {
        $queue = $this->queue($signal, false);
        if (false === $queue) return false;
        return $queue->dequeue($subscription);
    }

    /**
     * Locates a Queue object in storage, if not found one is created.
     *
     * @param  mixed  $signal  Signal the queue represents.
     * @param  boolean  $generate  Generate the queue if not found.
     *
     * @return  mixed  Queue object, false if generate is false and queue
     *          is not found.
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
            // this is skipped
            // @codeCoverageIgnoreStart
            }
            // @codeCoverageIgnoreEnd
        }

        if (!$generate) return false;

        if (!(is_object($signal) && $signal instanceof Signal)) {
            $signal = new Signal($signal);
        }

        $queue = new Queue($signal);

        // new queue
        if ($indexable) {
            $this->_index_storage[$index] = $queue;
        } else {
            $this->_non_index_storage[] = $queue;
        }
        return $queue;
    }

    /**
     * Fires an event signal.
     *
     * @param  mixed  $signal  The event signal, this can be the signal object
     *         or the signal representation.
     *
     * @param  array  $vars  Array of variables to pass the subscribers
     *
     * @param  object  $event  \prggmr\Event
     *
     * @param  array  $stacktrace  Stacktrace array
     *
     * @return  object  Event
     */
    public function fire($signal, $vars = null, $event = null, $stacktrace = null)
    {
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
                    'fire expected instance of Event received "%s"'
                , get_class($event))
            );
        }
        
        if (0 === count($vars)) {
            $vars = array(&$event);
        } else {
            $vars = array_merge(array(&$event), $vars);
        }

        $event->setState(Event::STATE_ACTIVE);
        
        // index lookup
        $obj = (is_object($signal) && $signal instanceof Signal);
        if (static::canIndex($signal)) {
            $index = ($obj) ? $signal->getSignal() : $signal;
            if (isset($this->_index_storage[$index])) {
                $_queue = $this->_index_storage[$index];
                // rewind only
                $_queue->rewind(false);
                while($_queue->valid()){
                    $queue->enqueue(
                        $_queue->current(), 
                        $_queue->getInfo()
                    );
                    $this->_index_storage[$index]->next();
                }
            }
        }
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
            $this->_fire($queue->getSignal(), $queue->current(), $vars);
            if (!$event->isHalted() &&
                null !== ($chain = $queue->getSignal()->getChain())) {
                foreach ($chain as $_chain) {
                    $link = $this->fire($_chain, $vars);
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
     * Flushes the engine and resets its state.
     *
     * @return void
     */
    public function flush(/* ... */)
    {
        $this->_non_index_storage = new \ArrayObject();
        $this->_index_storage = new \ArrayObject();
        $this->_timers = new \ArrayObject;
        $this->_state = Engine::RUNNING;
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
     * Returns if the provided param is indexable in a php array.
     *
     * @param  mixed  $param
     *
     * @return  boolean
     */
    public static function canIndex($param)
    {
        if (is_object($param) && $param instanceof Signal) {
            return $param->canIndex();
        }
        return is_int($param) || is_string($param);
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
     * Returns the current time in millseconds.
     *
     * @return  integer
     */
    public function getMilliseconds()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Returns the current engine state.
     *
     * @return  integer
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Starts the event loop.
     *
     * @param  boolean  $reset  Resets all timers to begin at loop start.
     * @param  integer  $timeout  Number of milliseconds to run the loop.
     *
     * @return  void
     */
    public function loop($reset = false, $timeout = null)
    {
        # Reset all timers
        if ($reset) {
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
        #
        # The loop now runs based on the available handlers. If nothing
        # is set to be handled the engine will shutdown, otherwise it will
        # continue to run.
        #
        # When running sleep time is determained based on the available 
        # handlers. If no signal handlers can handle and time handlers
        # are available the loop will sleep until the next timer needs
        # to trigger.
        #
        while($this->canHandle()) {
            # Signal shutdown based on the state
            $engine_state = $this->getState();
            if (static::SHUTDOWN === $engine_state ||
                static::ERROR === $engine_state) {
            }
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
                        $this->_fire(null, $_timer[0], $vars);
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
     * Fires a subscription.
     *
     * @param  object  $signal  Signal
     *
     * @param  object  $subscription  Subscription
     *
     * @param  array  $vars  Array of variables to pass the subscribers.
     *
     * @return  object  Event
     */
    protected function _fire($signal, $subscription, &$vars)
    {
        // TODO Is an additional object validation required at this point?
        $vars[0]->setSubscription($subscription);
        $result = $subscription->fire($vars);
        if (!$vars[0] instanceof \prggmr\Event) {
            throw new \RuntimeException(sprintf(
                'Event object has been replaced in subscription %s',
                $subscription->getIdentifier()
            ));
        }
        if (null !== $result) {
            // anything returned is set to the "return" value
            // note that it is greedy
            $vars[0]->setData($result, 'return');
            if (false === $result) {
                $vars[0]->halt();
            } 
        }
        if ($vars[0]->getState() == Event::STATE_ERROR) {
            if ($this->getState() === Engine::LOOP) {
                if (false === $this->clearInterval($subscription)) {
                    $this->dequeue($signal, $subscription);
                }
            } else {
                $this->dequeue($signal, $subscription);
            }
        }
        if ($subscription->isExhausted()) {
            $this->dequeue($signal, $subscription);
        }
        return $vars[0];
    }

    /**
     * Sends the engine the shutdown signal while in loop mode.
     *
     * @return  void
     */
    public function shutdown()
    {
        $this->_state = Engine::SHUTDOWN;
    }
}