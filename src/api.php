<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Creates a new signal handler.
 *
 * @param  object  $callable  Closure
 * @param  string|integer|object  $signal  Signal to attach the handle.
 * @param  integer $priority  Handle priority.
 * @param  integer  $exhaust  Handle exhaustion.
 *
 * @return  object|boolean  Handle, boolean if error
 */
function handle($closure, $signal, $priority = QUEUE_DEFAULT_PRIORITY, $exhaust = 1)
{
    return \prggmr::instance()->handle($closure, $signal, $priority, $exhaust);
}

/**
 * Remove a signal handler.
 *
 * @param  object  $handle  Handle instance.
 * @param  string|integer|object  $signal  Signal handle is attached to.
 *
 * @return  void
 */
function handle_remove($handle, $signal)
{
    return \prggmr::instance()->handle_remove($handle, $signal);   
}

/**
 * Registers a new signal handle loader which recursively loads files in the
 * given directory when a signal is triggered.
 * 
 * @param  integer|string|object  $signal  Signal to register with
 * @param  string  $directory  Directory to load handles from
 * @param  integer  $heap  Queue heap type
 * 
 * @return  object  \prggmr\Handle
 */
function handle_loader($signal, $directory, $heap = QUEUE_MIN_HEAP)
{
    return \prggmr::instance()->handle_loader($signal, $directory, $heap);
}

/**
 * Signal an event.
 *
 * @param  string|integer|object  $signal  Signal or a signal instance.
 * @param  array  $vars  Array of variables to pass the handles.
 * @param  object  $event  Event
 *
 * @return  object  \prggmr\Event
 */
function signal($signal, $vars = null, &$event = null)
{
    return \prggmr::instance()->signal($signal, $vars, $event);
}

/**
 * Returns the event history.
 * 
 * @return  array
 */
function event_history(/* ... */)
{
    return \prggmr::instance()->event_history();
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
 * @param  boolean  $create  Create the queue if not found.
 * @param  integer  $type  [QUEUE_MIN_HEAP,QUEUE_MAX_HEAP]
 *
 * @return  boolean|array  [QUEUE_NEW|QUEUE_EMPTY|QUEUE_NONEMPTY, queue, signal]
 */
function signal_queue($signal, $create = true, $type = QUEUE_MIN_HEAP)
{
    return \prggmr::instance()->signal_queue($signal);
}

/**
 * Calls a function at the specified intervals of time in milliseconds.
 *
 * @param  object  $function  Closure
 * @param  integer  $timeout  Milliseconds before calling timeout.
 * @param  array  $vars  Variables to pass the timeout function
 * @param  integer  $priority  Timeout priority
 * @param  integer|null  $exhaust  Exhaustion Rate | Default null
 *
 * @return  array  [signal, handle]
 */
function interval($function, $interval, $vars = null, $priority = QUEUE_DEFAULT_PRIORITY, $exhaust = null)
{
    $signal = new signal\time\Interval($interval, $vars);
    $handle = \prggmr::instance()->handle($function, $signal, $priority, $exhaust);
    return [$signal, $handle];
}

/**
 * Calls a timeout function after the specified time in microseconds.
 * 
 * @param  object  $function  Closure
 * @param  integer  $timeout  Milliseconds before calling timeout.
 * @param  array  $vars  Variables to pass the timeout function
 * @param  integer  $priority  Timeout priority
 * @param  integer|null  $exhaust  Exhaustion Rate | Default 1
 *
 * @return  array  [signal, handle]
 */
function timeout($function, $timeout, $vars = null, $priority = QUEUE_DEFAULT_PRIORITY, $exhaust = 1)
{
    $signal = new signal\time\Timeout($timeout, $vars);
    $handle = \prggmr::instance()->handle($function, $signal, $priority, $exhaust);
    return [$signal, $handle];
}

/**
 * Starts the prggmr event loop.
 *
 * @param  null|integer  $ttr  Number of milliseconds to run the loop. 
 *
 * @return  void
 */
function loop($ttr = null)
{
    return \prggmr::instance()->loop($ttr);
}

/**
 * Sends the loop the shutdown signal.
 *
 * @return  void
 */
function shutdown()
{
    return \prggmr::instance()->shutdown();
}

/**
 * Load a signal library.
 * 
 * @param  string  $name  Signal library name.
 * @param  string|null  $dir  Location of the library. 
 * 
 * @return  void
 */
function load_signal($name, $dir = null) 
{
    return \prggmr::instance()->load_signal($name, $dir);
}

/**
 * Registers a function to interupt the signal stack before or after a 
 * signal fires.
 * 
 * @param  object  $handle  Handle to execute
 * @param  string|object  $signal
 * @param  int|null  $place  Interruption location. prggmr\Engine::INTERRUPT_PRE|prggmr\Engine::INTERRUPT_POST
 * @param  int|null  $priority  Interrupt priority
 * @param  boolean  $complex  Register the given complex signal as a complex interrupt signal
 * 
 * @return  boolean  True|False false is failure
 */
function signal_interrupt($handle, $signal, $interrupt = null, $priority = null, $complex = false) 
{
    return \prggmr::instance()->signal_interrupt($handle, $signal, $interrupt, $priority, $complex);
}

/**
 * Returns the prggmr object instance.
 * 
 * @return  object  prggmr\Engine
 */
function prggmr()
{
    return \prggmr::instance();
}