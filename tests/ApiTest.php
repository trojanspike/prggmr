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
 * \prggmr\Event Unit Tests
 */

include_once 'bootstrap.php';
include_once 'EngineTest.php';

class ApiTest extends EngineTest
{
    public function testInstance()
    {
        $prggmr = Prggmr::instance();
        $this->assertInstanceof('Prggmr', $prggmr);
    }

    public function setUp()
    {
        $this->engine = Prggmr::instance();
    }

    public function testVersion()
    {
        $this->assertEquals(Prggmr::version(), PRGGMR_VERSION);
    }

    public function testSubscribeFire()
    {
        subscribe('test', function($event){
            $event->setData('test');
        });
        $event = fire('test');
        $this->assertEquals(array('test'), $event->getData());
    }

    public function testChains()
    {
        subscribe('chain', function($event){
            $event->setData('chain');
        });
        subscribe('chain_1', function($event){
            $event->setData('chain_1');
        });
        subscribe('chain_2', function($event){
            $event->setData('chain_2');
        });
        subscribe('chain_3', function($event){
            $event->setData('chain_3');
        });
        chain('chain', 'chain_1');
        chain('chain', 'chain_2');
        chain('chain_1', 'chain_2');
        chain('chain_2', 'chain_3');
        $event = fire('chain');
        $this->assertEquals(array('chain'), $event->getData());
        $link1 = $event->getChain();
        $this->assertType('array', $link1);
        $this->assertEquals(2, count($link1));

        // FIRST CHAIN LINK 1
        $this->assertInstanceOf('\prggmr\Event', $link1[0]);
        $this->assertEquals('chain_1', $link1[0]->getSignal()->signal());
        $this->assertEquals(array('chain_1'), $link1[0]->getData());

        // FIRST CHAIN LINK 1 LINK 1
        $link1c1 = $link1[0]->getChain();
        $this->assertType('array', $link1c1);
        $this->assertEquals('chain_2', $link1c1[0]->getSignal()->signal());
        $this->assertEquals(array('chain_2'), $link1c1[0]->getData());

        // FIRST CHAIN LINK 1 LINK 1 LINK 1
        $link1c1c1 = $link1c1[0]->getChain();
        $this->assertType('array', $link1c1c1);
        $this->assertEquals('chain_3', $link1c1c1[0]->getSignal()->signal());
        $this->assertEquals(array('chain_3'), $link1c1c1[0]->getData());

        // FIRST CHAIN LINK 2
        $this->assertInstanceOf('\prggmr\Event', $link1[1]);
        $this->assertEquals('chain_2', $link1[1]->getSignal()->signal());
        $this->assertEquals(array('chain_2'), $link1[1]->getData());

        // FIRST CHAIN LINK 2 LINK 1
        $link2c1 = $link1[1]->getChain();
        $this->assertType('array', $link2c1);
        $this->assertEquals('chain_3', $link2c1[0]->getSignal()->signal());
        $this->assertEquals(array('chain_3'), $link2c1[0]->getData());

        subscribe('test_dechain', function(){;});
        subscribe('test_dechain1', function(){;});
        chain('test_dechain', 'test_dechain1');
        dechain('test_dechain', 'test_dechain1');
        $dechain = fire('test_dechain');
        $chain = $dechain->getChain();
        $this->assertNull($chain);
    }

    public function testOnce()
    {
        once('test_once', function(){;}, 'id');
        $this->assertTrue(Prggmr::instance()->queue('test_once')->locate('id'));
        fire('test_once');
        $this->assertFalse(Prggmr::instance()->queue('test_once')->locate('id'));
    }

    public function testTimersAndDaemon()
    {
        //Prggmr::instance()->flush();
        //$count = 1;
        //setInterval(function($event, $count, $unit) {
        //    echo ".";
        //    $count++;
        //    $unit->addToAssertionCount(1);
        //    if ($count >= 5) {
        //        setTimeout(function($event, $unit){
        //            clearInterval('intervalTest');
        //        }, 1000, &$unit);
        //    }
        //}, 1000, array(&$count, &$this), 'intervalTest');
        //setTimeout(function($event, $unit){
        //    prggmrd_shutdown();
        //}, 1000 * 7, &$unit);
        //prggmrd(true);
        //$this->assertEquals(\prggmr\Engine::SHUTDOWN, Prggmr::instance()->getState());
        //$this->assertEquals(5, $count);
    }
}