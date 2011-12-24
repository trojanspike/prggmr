<?php
/**
 *  Copyright 2010-11 Nickolas Whiting
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
 * @copyright  Copyright (c), 2010-11 Nickolas Whiting
 */

/**
 * Assertions designed for the unit testing prggmr.
 */

/**
 * Asserts an event by firing the event signal using the provided engine
 * checking the event return and data associated.
 */
assertion(function($test, $engine, $signal, $params, $expected){
    
    $event = $engine->fire($signal, $params);

    if (!$event instanceof \prggmr\Event) {
        return sprintf('%s is not a prggmr\Event object',
            \prggmrunit\Output::variable($event)
        );
    }

    if ($event->getData() !== $expected) {
        return sprintf('%s does not equal expected %s',
            \prggmrunit\Output::variable($event->getData()),
            \prggmrunit\Output::variable($expected)
        );
    }

    return true;

}, 'strict_event');
