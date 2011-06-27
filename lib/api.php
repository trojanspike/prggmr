<?php
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


/**
* Attaches a new subscription to a signal queue.
*
* @param  mixed  $signal  Signal the subscription will attach to.
*
* @param  mixed  $subscription  PHP callable.
*
* @param  mixed  $priority  Priority of this subscription within the Queue
*
* @param  array  $config  Array of configuration parameters.
*
* 		   Options:
*
* 		   'identifier': string identifier for subscription.
* 		   
* 		   'priority':   priority to fire this subcription
* 		   
* 		   'chain':      signal to chain on fire
* 		   
* 		   'exhaust':    number of times to fire subscription before
* 		   				 exhaustion
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
function subscribe($signal, $subscription, $config = array())
{
	return \prggmr\Engine::instance()->subscribe($signal, $subscription, $config);
}

/**
* Attaches a new subscription to a signal queue.
*
* @param  mixed  $signal  Signal the subscription will attach to.
*
* @param  mixed  $subscription  PHP callable.
*
* @param  mixed  $priority  Priority of this subscription within the Queue
*
* @param  array  $config  Array of configuration parameters.
*
* 		   Options:
*
* 		   'identifier': string identifier for subscription.
* 		   
* 		   'priority':   priority to fire this subcription
* 		   
* 		   'chain':      signal to chain on fire
* 		   
* 		   'exhaust':    number of times to fire subscription before
* 		   				 exhaustion
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
function on($signal, $subscription, $config = array())
{
	return \prggmr\Engine::instance()->subscribe($signal, $subscription, $config);
}

/**
* Subscribe to a function once and exhaust.
*
* @param  mixed  $signal  Signal the subscription will attach to.
*
* @param  mixed  $subscription  PHP callable.
*
* @param  array  $config  Array of configuration parameters.
*
* 		   Options:
*
* 		   'identifier': string identifier for subscription.
* 		   
* 		   'priority':   priority to fire this subcription
* 		   
* 		   'chain':      signal to chain on fire
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*/
function once($signal, $subscription, $config = array())
{
	$config['exhaust'] = 1;
	return \prggmr\Engine::instance()->subscribe($signal, $subscription, $config);
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
function fire($signal, $vars = null, $event = null)
{
	return \prggmr\Engine::instance()->fire($signal, $vars, $event);
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
function emit($signal, $vars = null, $event = null)
{
	return \prggmr\Engine::instance()->fire($signal, $vars, $event);
}