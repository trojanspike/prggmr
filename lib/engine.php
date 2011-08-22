<?php
namespace prggmr;
/**
 *  Copyright 2010 Nickolas Whiting
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
 * @author  Nickolas Whiting  <me@nwhiting.com>
 * @package  prggmr
 * @copyright  Copyright (c), 2010 Nickolas Whiting
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
    protected $_indexStorage = null;

    /**
     * A non index storage of Queue
     *
     * @var  array
     */
    protected $_storage = null;

    /**
     * Timer based events
     *
     * @var array
     */
    protected $_timers = array();

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
    const DAEMON   = 0x65;
    const SHUTDOWN = 0x66;
    const ERROR    = 0x67;

    /**
     * Daemon shutdown timeout.
     */
    const DAEMON_SHUTDOWN_TIMEOUT = 'daemon_shutdown';

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
        $this->_storage = array();
        $this->_indexStorage = array();
        $this->_state = Engine::RUNNING;
    }

    /**
     * Attaches a new subscription to a signal queue.
     *
     * @param  mixed  $signal  Signal the subscription will attach to, this
     *         can be a Signal object, the signal representation or an array
     *         for a chained signal.
     *
     * @param  mixed  $subscription  Subscription closure that will trigger on
     *         fire or a Subscription object.
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
    public function subscribe($signal, $subscription, $identifier = null, $priority = null, $chain = null, $exhaust = 0)
    {
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
            if (isset($this->_indexStorage[$index])) {
                return $this->_indexStorage[$index];
            }
            $indexable = true;
        } else {
            $length = count($this->_storage);
            for($i=0;$i!=$length;$i++) {
                if (($obj && $this->_storage[$i]->getSignal() === $signal) ||
                ($this->_storage[$i]->getSignal(true) === $signal)) {
                    return $this->_storage[$i];
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
            $this->_indexStorage[$index] = $queue;
        } else {
            $this->_storage[] = $queue;
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
     * @param  object  $event  Event
     *
     * @return  object  Event
     */
    public function fire($signal, $vars = null, $event = null)
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
                    'fire expected instance of Event recieved "%s"'
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
            if (isset($this->_indexStorage[$index])) {
                $_queue = $this->_indexStorage[$index];
                // rewind only
                $_queue->rewind(false);
                while($_queue->valid()){
                    $queue->enqueue(
                        $_queue->current(), 
                        $_queue->getInfo()
                    );
                    $this->_indexStorage[$index]->next();
                }
            }
        }
        $length = count($this->_storage);
        if (0 !== $length) {
            for($i=0;$i!=$length;$i++) {
                if (false !==
                    ($compare = $this->_storage[$i]->getSignal()->compare($signal))) {
                    // rewind only
                    $this->_storage[$i]->rewind(false);
                    while($this->_storage[$i]->valid()){
                        $this->_storage[$i]->current()->params(&$compare);
                        $queue->enqueue(
                            $this->_storage[$i]->current(), 
                            $this->_storage[$i]->getInfo()
                        );
                        $this->_storage[$i]->next();
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
        
        while($queue->valid()) {
            $event->setSignal($queue->getSignal());
            if ($event->isHalted()) break;
            $this->_fire($queue->getSignal(), $queue->current(), &$vars);
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
     * Flushes the engine.
     */
    public function flush(/* ... */)
    {
        $this->_storage = array();
        $this->_indexStorage = array();
        $this->_timers = array();
    }

    /**
     * Returns the count of subsciption queues in the engine.
     *
     * @return  integer
     */
    public function count()
    {
        return count($this->_storage) + count($this->_indexStorage);
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
     * Calls an event at the specified intervals of time in microseconds.
     *
     * @param  mixed  $subscription  Subscription closure that will trigger on
     *         fire or a Subscription object.
     *
     * @param  integer  $interval  Interval of time in microseconds to run
     *
     * @param  mixed  $vars  Variables to pass the interval.
     *
     * @param  string  $identifier  Identifier of this subscription.
     *
     * @param  integer  $exhaust  Count to set subscription exhaustion.
     *
     * @throws  InvalidArgumentException  Thrown when an invalid callback or
     *          interval is provided.
     *
     * @return  object  Subscription
     */
    public function setInterval($subscription, $interval, $vars = null, $identifier = null, $exhaust = 0)
    {
        if (!$subscription instanceof Subscription) {
            if (!is_callable($subscription)) {
                throw new \InvalidArgumentException(
                    'subscription callback is not a valid callback'
                );
            }
            $subscription = new Subscription($subscription, $identifier, $exhaust);
        }

        if (!is_int($interval)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'invalid time interval expected integer recieved %s',
                    gettype($interval)
                )
            );
        }

        $this->_timers[] = array($subscription, $interval, $this->getMilliseconds() + $interval, $vars);
        return $subscription;
    }

    /**
     * Calls an event after the specified amount of time in microseconds.
     *
     * @param  mixed  $subscription  Subscription closure that will trigger on
     *         fire or a Subscription object.
     *
     * @param  integer  $interval  Interval of time in microseconds to run
     *
     * @param  mixed  $vars  Variables to pass the timeout.
     *
     * @param  string  $identifier  Identifier of this subscription.
     *
     * @param  integer  $exhaust  Count to set subscription exhaustion.
     *
     * @throws  InvalidArgumentException  Thrown when an invalid callback or
     *          interval is provided.
     *
     * @return  object  Subscription
     */
    public function setTimeout($subscription, $interval, $vars = null, $identifier = null)
    {
        // This simply uses set interval and sets an exhaustion rate of 1 ...
        return $this->setInterval($subscription, $interval, $vars, $identifier, 1);
    }

    /**
     * Clears an interval set by setInterval.
     *
     * @param  mixed  $subscription  Subscription object of the interval or
     *         identifer.
     *
     * @return  void
     */
    public function clearInterval($subscription)
    {
        $timers = count($this->_timers);
        $obj = (is_object($subscription) && $subscription instanceof Subscription);
        foreach($this->_timers as $_index => $_timer) {
            if (($obj && $_timer[0] === $subscription) ||
                ($_timer[0]->getIdentifier() === $subscription)) {
                unset($this->_timers[$_index]);
                return true;
            }
        }
        return false;
    }

    /**
     * Clears a timeout set by setTimeout.
     *
     * @param  mixed  $subscription  Subscription object of the timeout or
     *         identifer.
     *
     * @return  void
     */
    public function clearTimeout($subscription)
    {
        $this->clearInterval($subscription);
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
     * Starts daemon mode.
     *
     * @param  boolean  $reset  Resets all timers to begin at daemon start.
     * @param  integer  $timeout  Number of milliseconds to run the daemon.
     *
     * @return  void
     */
    public function daemon($reset = false, $timeout = null)
    {
        if ($reset) {
            $timers = count($this->_timers);
            foreach($this->_timers as $_index => $_timer) {
                $this->_timers[$_index][2] = $this->getMilliseconds() + $this->_timers[$_index][1];
            }
        }
        // this can be cleared using clearTime(Engine::DAEMON_SHUTDOWN_TIMEOUT)
        // but after that the daemon will run indefinitly
        if (null !== $timeout && is_int($timeout)) {
            // this is a required hack ... i know
            // php 5.4 will hopefully provide a fix
            $engine = $this;
            $this->setTimeout(function() use ($engine) {
                $engine->shutdown();
            }, $timeout, null, Engine::DAEMON_SHUTDOWN_TIMEOUT);
        }
        while(true) {
            usleep(100);
            if ($this->getState() === Engine::SHUTDOWN||
                $this->getState() ===  Engine::ERROR) {
                break;
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
                        $this->_fire(null, $_timer[0], &$vars);
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

    // @codeCoverageIgnoreStart

    /**
     * ===== EXPERIMENTAL ======
     *
     * THIS METHOD IS CURRENTLY COMPLETELY UNTESTED IN EARLY PLANNING
     * STAGES AND MAY NOT REFLECT THE FINAL FUNCTIONALITY
     *
     * Keeps a running record of an event stacktrace which is built
     * in reverse when an error is encountered.
     *
     * This method is a debugging method and highly recommended
     * aganist use while in a production environment as it is quite
     * an expensive call.
     *
     * @param  object  $event  Event which is called.
     * @param  boolean  $return  Return the stacktrace for the event
     *
     * @return  void
     */
    protected function _stacktrace($event, $return = false)
    {
        // debug setting
        if (!PRGGMR_DEBUG) return null;
        $hash = spl_object_hash($event);
        if ($return) return $this->_stacktrace[$hash];
        // we have to return the entire trace ... which may be expensive
        // but until php 5.4 there isnt much that can be done except
        // running a debug mode
        $this->_stacktrace[$hash][] = end(
            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        );
    }

    // @codeCoverageIgnoreEnd

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
    protected function _fire($signal, $subscription, $vars)
    {
        // TODO Is an additional object validation required at this point?
        $vars[0]->setSubscription($subscription);
        $result = $subscription->fire($vars);
        if (false === $result) {
            $vars[0]->halt();
        } elseif (true !== $result && null !== $result) {
            // anything returned is set to the "return" value
            // note that it is greedy
            $vars[0]->setData($result, 'return');
        }
        if ($vars[0]->getState() == Event::STATE_ERROR) {
            if ($this->getState() === Engine::DAEMON) {
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
     * Sends the engine the shutdown signal while in daemon mode.
     *
     * @return  void
     */
    public function shutdown()
    {
        $this->_state = Engine::SHUTDOWN;
    }
}