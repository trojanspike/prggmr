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
 * prggmrunit signal unit tests
 */
suite(function($suite){
    
    test(function($test){
        $signal = new \prggmr\Signal('helloworld');
        $test->true($signal->compare('helloworld'));
        $test->false($signal->compare('HelloWorld'));
    }, 'string test');
    
    test(function($test){
        $signal = new \prggmr\Signal(array(
            0 => 'test'
        ));
        $test->true($signal->compare(array(
            0 => 'test'
        )));
        $test->false($signal->compare(array(
            1 => 'test'
        )));
    }, 'array test');
    
    test(function($test){
        $obj = new \stdClass();
        $obj->hello = 'world';
        $signal = new \prggmr\Signal($obj);
        $test->true($signal->compare($obj));
        $obj = new \stdClass();
        $obj->hello = 'wORld';
        $test->false($signal->compare($obj));
    }, 'testObjectSignal');
    
    test(function($test){
        $signal = new \prggmr\Signal(true);
        $test->true($signal->compare(true));
        $test->false($signal->compare(1));
        $test->false($signal->compare(''));
    }, 'Boolean True Signal');
    
    test(function($test){
        $signal = new \prggmr\Signal(false);
        $test->true($signal->compare(false));
        $test->false($signal->compare(0));
    }, 'Boolean False Signal');
    
    
    test(function($test){
        $signal = new \prggmr\Signal(100);
        $test->true($signal->compare(100));
        $test->false($signal->compare('100'));
    }, 'Integer Signal');
    
    test(function($test){
        $signal = new \prggmr\Signal(100.2);
        $test->true($signal->compare(100.2));
        $test->false($signal->compare('100.2'));
    }, 'Float Signal');
    
    test(function($test){
        $signal = new \prggmr\Signal('test');
        $test->null($signal->getChain());
        $signal->setChain('chain_1');
        $test->equal(array('chain_1'), $signal->getChain());
        $signal->setChain('chain_2');
        $test->equal(array('chain_1', 'chain_2'), $signal->getChain());
        $signal->delChain('chain_1');
        $test->equal(array('chain_2'), $signal->getChain());
        $signal->delChain('chain_2');
        $test->null($signal->getChain());
    }, 'Chains');
    
}, 'Signal Testing Suite');
