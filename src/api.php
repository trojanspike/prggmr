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
* Create a new sig handler.
*
* @param  mixed  $signal  Signal to handle, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Handle closure that will trigger.
*
* @param  string  $identifier  Identify of the handle.
*
* @param  integer $priority  Priority of the handle.
*
* @param  mixed  $chain  Chain thrown from this handle.
*
* @param  integer  $exhaust  Rate at which this handle will exhaust.
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
* Attaches a new handle to a signal for one execution loop.
*
* @param  mixed  $signal  Signal the handle will attach to, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Handle closure that will trigger.
*
* @param  string  $identifier  Identify of the handle.
*
* @param  integer $priority  Priority of the handle.
*
* @param  mixed  $chain  Chain thrown from this handle.
*
* @param  integer  $exhaust  Rate at which this handle will exhaust.
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
* Removes a handle from a signal.
*
* @param  mixed  $signal  Signal handle is attached to.
*
* @param  mixed  $handle  handle instance or id.
*
* @throws  InvalidArgumentException
* @return  void
*/
if (!function_exists('dequeue')){
function handle_remove($signal, $handle)
{
    return prggmr::instance()->dequeue($signal, $handle);   
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
    return prggmr::instance()->queue($signal)->getSignal()->removeChain($chain);
}
}

/**
* Signals an event.
*
* @param  mixed  $signal  Signal instance or signal.
*
* @param  array  $vars  Array of variables to pass the handles.
*
* @param  object  $event  Event
*
* @return  object  Event
*/
if (!function_exists('fire')){
function signal($signal, $vars = null, &$event = null)
{
    if (PRGGMR_DEBUG) {
        if (version_compare(phpversion(), '5.3.6', '>=')) {
            return prggmr::instance()->signal($signal, $vars, $event, debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
        } else {
            return prggmr::instance()->signal($signal, $vars, $event, debug_backtrace());
        }
    } else {
        return prggmr::instance()->signal($signal, $vars, $event);
    }
}
}

/**
 * Calls a function at the specified intervals of time in microseconds.
 *
 * @param  mixed  $callable  Callable php variable.
 *
 * @param  integer  $interval  Interval of time in microseconds between execution.
 *
 * @param  mixed  $vars  Variables to pass the interval.
 *
 * @param  string  $identifier  Identifier of the function.
 *
 * @param  integer  $exhaust  Rate at which this handle will exhaust.
 *
 * @param  mixed  $start  Unix parse able date to start the function interval.
 *
 * @throws  InvalidArgumentException  Thrown when an invalid callback,
 *          interval or un-parse able date is provided.
 *
 * @return  object  \prggmr\handle\Time
 */
if (!function_exists('setInterval')){
function setInterval($function, $interval, $vars = null, $identifier = null, $exhaust = 0, $start = null)
{
    return prggmr::instance()->setInterval($function, $interval, $vars, $identifier, $exhaust, $start);
}
}

/**
 * Calls a function after the specified time in microseconds.
 *
 * @param  mixed  $callable  Callable php variable.
 *
 * @param  integer  $interval  Number of microseconds to pass before execution.
 *
 * @param  mixed  $vars  Variables to pass.
 *
 * @param  string  $identifier  Identifier of the function.
 *
 * @param  mixed  $start  Unix parse able date to start the function.
 *
 * @throws  InvalidArgumentException  Thrown when an invalid callback,
 *          interval or un-parse able date is provided.
 *
 * @return  object  \prggmr\handle\Time
 */
if (!function_exists('setTimeout')){
function setTimeout($function, $timeout, $vars = null, $identifier = null, $start = null)
{
    // This simply uses set interval and sets an exhaustion rate of 1 ...
    return prggmr::instance()->setTimeout($function, $timeout, $vars, $identifier, $start);
}
}

/**
 * Clears an interval.
 *
 * @param  mixed  $subscription  Time handle instance of identifier.
 *
 * @return  void
 */
if (!function_exists('clearInterval')){
function clearInterval($handle)
{
    return prggmr::instance()->clearInterval($handle);
}
}

/**
 * Clears a timeout.
 *
 * @param  mixed  $subscription  Time handle instance of identifier.
 *
 * @return  void
 */
if (!function_exists('clearTimeout')){
function clearTimeout($handle)
{
    return prggmr::instance()->clearTimeout($handle);
}
}

/**
 * Starts the prggmr event loop.
 *
 * @param  boolean  $reset  Resets all timers to begin at loop start.
 * @param  integer  $timeout  Number of milliseconds to run the loop. 
 *
 * @return  void
 */
if (!function_exists('prggmr_loop')){
function prggmr_loop($reset = false, $timeout = null)
{
    return prggmr::instance()->loop($reset, $timeout);
}
}

/**
 * Sends the loop the shutdown signal.
 *
 * @return  void
 */
if (!function_exists('shutdown')){
function prggmr_shutdown()
{
    return prggmr::instance()->shutdown();
}
}