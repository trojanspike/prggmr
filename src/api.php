<?php
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
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
* @param  integer $priority  Priority of the handle.
*
* @param  integer  $exhaust  Rate at which this handle will exhaust.
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
if (!function_exists('handle')){
function handle($subscription, $signal, $priority = null, $exhaust = 1)
{
    return prggmr::instance()->handle($subscription, $signal, $priority, $exhaust);
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
* @return  void
*/
if (!function_exists('once')){
function handle_once($subscription, $signal, $priority = null)
{
    return prggmr::instance()->handle($signal, $subscription, $priority, 1);
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
    return prggmr::instance()->signal($signal, $vars, $event);
}
}

/**
 * Calls a function at the specified intervals of time in microseconds.
 *
 * @param  object  $function  Closure
 * @param  integer  $timeout  Milliseconds before calling timeout.
 * @param  array  $vars  Variables to pass the timeout function
 * @param  integer  $priority  Timeout priority
 *
 * @return  array  [signal, handle]
 */
if (!function_exists('interval')){
function interval($function, $interval, $vars = null, $priority = QUEUE_DEFAULT_PRIORITY)
{
    $signal = new \prggmr\signal\Interval($interval, $vars);
    $handle = prggmr::instance()->handle($function, $signal, $priority, null);
    return [$signal, $handle];
}
}

/**
 * Calls a timeout function after the specified time in microseconds.
 * 
 * @param  object  $function  Closure
 * @param  integer  $timeout  Milliseconds before calling timeout.
 * @param  array  $vars  Variables to pass the timeout function
 * @param  integer  $priority  Timeout priority
 *
 * @return  array  [signal, handle]
 */
if (!function_exists('timeout')){
function timeout($function, $timeout, $vars = null, $priority = QUEUE_DEFAULT_PRIORITY)
{
    $signal = new \prggmr\signal\Time($timeout, $vars);
    $handle = prggmr::instance()->handle($function, $signal, $priority, 1);
    return [$signal, $handle];
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
function prggmr_loop()
{
    return prggmr::instance()->loop();
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