<?php
namespace prggmr\signal\time;

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