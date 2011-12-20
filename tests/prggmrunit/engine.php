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
 * prggmrunit engine unit tests
 */
suite(function(){

    setup(function($test){
        $test->engine = new \prggmr\Engine();
    });

    teardown(function($test){
        $test->engine->flush();
    });

    test(function($test){
        $test->engine->subscribe('subscriber', function(){}, 'testSubscribe');
        $test->true($test->engine->count() == 1);
    }, 'Subscribing');

    test(function($test){
        $test->engine->subscribe(
            'subscribe-parameter-single', function($event, $param1){
            $event->setData($param1);
        }, 'testEventSingleParameter');
        $test->strict_event($test->engine, 'subscribe-parameter-single',
            array('helloorld'), array('helloworld')
        );
    }, 'Subscription with Parameters');


}, 'Engine Test Suite');
