<?php
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

/**
* Attaches a new handler to a signal.
*
* @param  mixed  $signal  Signal the handler will attach to, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Handle closure that will trigger.
*
* @param  string  $identifier  Identify of the handler.
*
* @param  integer $priority  Priority of the handler.
*
* @param  mixed  $chain  Chain thrown from this handler.
*
* @param  integer  $exhaust  Rate at which this handler will exhaust.
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
if (!function_exists('handle')){
function handle($subscription, $signal, $identifier = null, $priority = null, $chain = null, $exhaust = 0)
{
    return prggmr::instance()->handle($subscription, $signal, $identifier, $priority, $chain, $exhaust);
}
}

/**
* Attaches a new handler to a signal for one execution loop.
*
* @param  mixed  $signal  Signal the handler will attach to, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Handle closure that will trigger.
*
* @param  string  $identifier  Identify of the handler.
*
* @param  integer $priority  Priority of the handler.
*
* @param  mixed  $chain  Chain thrown from this handler.
*
* @param  integer  $exhaust  Rate at which this handler will exhaust.
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
if (!function_exists('once')){
function handle_once($subscription, $signal, $identifier = null, $priority = null, $chain = null)
{
    return prggmr::instance()->handle($signal, $subscription, $identifier, $priority, $chain, 1);
}
}

/**
* Removes a handler from a signal.
*
* @param  mixed  $signal  Signal handler is attached to.
*
* @param  mixed  $handler  Handler instance or id.
*
* @throws  InvalidArgumentException
* @return  void
*/
if (!function_exists('dequeue')){
function dequeue($signal, $handler)
{
    return prggmr::instance()->dequeue($signal, $handler);   
}
}

/**
 * Establishes a chain between two signals.
 *
 * @param  mixed  $singal  Signal which triggers the chain.
 * @param  mixed  $chain  Signal to be chained.
 *
 * @return  void
 */
if (!function_exists('chain')){
function chain($signal, $chain)
{
    return prggmr::instance()->queue($signal)->getSignal()->setChain($chain);
}
}

/**
 * Removes a signal chain.
 *
 * @param  mixed  $singal  Signal which contains the chain
 * @param  mixed  $chain  Signal to be removed
 *
 * @return  void
 */
if (!function_exists('dechain')){
function dechain($signal, $chain)
{
    return prggmr::instance()->queue($signal)->getSignal()->delChain($chain);
}
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
if (!function_exists('fire')){
function fire($signal, $vars = null, &$event = null)
{
    if (PRGGMR_DEBUG) {
        if (version_compare(phpversion(), '5.3.6', '>=')) {
            return prggmr::instance()->fire($signal, $vars, $event, debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
        } else {
            return prggmr::instance()->fire($signal, $vars, $event, debug_backtrace());
        }
    } else {
        return prggmr::instance()->fire($signal, $vars, $event);
    }
}
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
if (!function_exists('setInterval')){
function setInterval($subscription, $interval, $vars = null, $identifier = null, $exhaust = 0, $start = null)
{
    return prggmr::instance()->setInterval($subscription, $interval, $vars, $identifier, $exhaust, $start);
}
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
if (!function_exists('setTimeout')){
function setTimeout($subscription, $interval, $vars = null, $identifier = null, $start = null)
{
    // This simply uses set interval and sets an exhaustion rate of 1 ...
    return prggmr::instance()->setTimeout($subscription, $interval, $vars, $identifier, $start);
}
}

/**
 * Clears an interval set by setInterval.
 *
 * @param  mixed  $subscription  Subscription object of the interval or
 *         identifer.
 *
 * @return  void
 */
if (!function_exists('clearInterval')){
function clearInterval($subscription)
{
    return prggmr::instance()->clearInterval($subscription);
}
}

/**
 * Clears a timeout set by setTimeout.
 *
 * @param  mixed  $subscription  Subscription object of the timeout or
 *         identifer.
 *
 * @return  void
 */
if (!function_exists('clearTimeout')){
function clearTimeout($subscription)
{
    return prggmr::instance()->clearTimeout($subscription);
}
}

/**
 * Starts prggmrs event loop.
 *
 * @param  boolean  $reset  Resets all timers to begin at loop start.
 * @param  integer  $timeout  Number of milliseconds to run the loop. 
 *
 * @return  void
 */
if (!function_exists('prggmr')){
function prggmr($reset = false, $timeout = null)
{
    return prggmr::instance()->loop($reset, $timeout);
}
}

/**
 * Sends the engine the shutdown signal while in loop mode.
 *
 * @return  void
 */
if (!function_exists('shutdown')){
function shutdown()
{
    return prggmr::instance()->shutdown();
}
}