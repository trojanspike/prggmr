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
 * \prggmr\Event Unit Tests
 */

include_once 'bootstrap.php';

suite(function(){

    test(function($test){
        $signal = new \prggmr\ArrayContainsSignal(array(
            'test', 'signal'
        ));
        $test->array($signal->signal());
        $test->equal(array('test','signal'), $signal->signal());
    }, 'Signal Construction');
    
    test(function($test){
        $signal = new \prggmr\ArrayContainsSignal(array(
            'test', 'signal'
        ));
        $test->true($signal->compare('test'));
        $test->true($signal->compare('signal'));
        $test->true($signal->compare('TesT'));
        $test->true($signal->compare('SiGNAL'));
        $test->false($signal->compare('unknown'));
    }, 'Compare');

    test(function($test){
        $signal = new \prggmr\ArrayContainsSignal(array(
            'test', 'signal'
        ), true);
        $test->array($signal->signal());
        $test->equal(array('test','signal'), $signal->signal());
    }, 'Signal Construction');
    
    test(function($test){
        $signal = new \prggmr\ArrayContainsSignal(array(
            'test', 'signal'
        ), true);
        $test->true($signal->compare('test'));
        $test->true($signal->compare('signal'));
        $test->false($signal->compare('unknown'));
        $test->false($signal->compare('TEST'));
        $test->false($signal->compare('sigNal'));
    }, 'Compare');

});