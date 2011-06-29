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
* @param  mixed  $signal  Signal the subscription will attach to, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Subscription closure that will trigger on
*         fire or a Subscription object.
*
* @param  string  $identifier  String identifier of this subscription.
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
* @return  void
*/
function subscribe($signal, $subscription, $identifier = null, $priority = null, $chain = null, $exhaust = 0)
{
	return Prggmr::instance()->subscribe($signal, $subscription, $identifier, $priority, $chain, $exhaust);
}

/**
* Attaches a new subscription to a signal queue with an exhaust rate of 1.
*
* @param  mixed  $signal  Signal the subscription will attach to, this
*         can be a Signal object, the signal representation or an array
*         for a chained signal.
*
* @param  mixed  $subscription  Subscription closure that will trigger on
*         fire or a Subscription object.
*
* @param  string  $identifier  String identifier of this subscription.
*
* @param  integer $priority  Priority of the subscription
*
* @param  mixed  $chain  Chain signal
*
* @throws  InvalidArgumentException  Thrown when an invalid callback is
*          provided.
*
* @return  void
*/
function once($signal, $subscription, $identifier = null, $priority = null, $chain = null)
{
	return Prggmr::instance()->subscribe($signal, $subscription, $identifier, $priority, $chain, 1);
}

/**
 * Creates a signal chain.
 *
 * @param  mixed  $singal  Signal which triggers the chain
 * @param  mixed  $chain  Signal to be chained
 *
 * @return  void
 */
function chain($signal, $chain)
{
    return Prggmr::instance()->queue($signal)->getSignal()->setChain($chain);
}

/**
 * Removes a signal chain.
 *
 * @param  mixed  $singal  Signal which contains the chain
 * @param  mixed  $chain  Signal to be removed
 *
 * @return  void
 */
function dechain($signal, $chain)
{
    return Prggmr::instance()->queue($signal)->getSignal()->delChain($chain);
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
	return Prggmr::instance()->fire($signal, $vars, $event);
}