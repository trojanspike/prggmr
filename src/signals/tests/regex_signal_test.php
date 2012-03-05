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
        $signal = new \prggmr\RegexSignal('hello.:world');
        $test->equal('#hello.(?P<world>[\w_-]+)$#i', $signal->signal());
    }, 'Signal Construction');
    
    test(function($test){
        $signal = new \prggmr\RegexSignal('hello.:world');
        $test->equal(array(
            'test'
        ), $signal->compare('hello.test'));
    }, 'Signal Inline Var Match');
    
    test(function($test){
        $signal = new \prggmr\RegexSignal('added.:action.:id.:from');
        $test->equal(array(
            'user',
            '7',
            'administration'
        ), $signal->compare('added.user.7.administration'));
    }, 'Inline Multiple Vars');

    test(function($test){
        $signal = new \prggmr\RegexSignal('regular.[\w_-]+');
        $test->true($signal->compare('regular.test'));
    }, 'No Return True');

    test(function($test){
        $signal = new \prggmr\RegexSignal('regular.expre.no[\w_-]+');
        $test->false($signal->compare('nothing'));
    }, 'No Match');

    test(function($test){
        $signal = new \prggmr\RegexSignal('test.([\w_-]+).(?<user>[\w_-]+).(.*)');
        $test->equal(
            array(
                'one',
                'two',
                'three'
            ), $signal->compare('test.one.two.three'));
    }, 'Matches Combination');
});