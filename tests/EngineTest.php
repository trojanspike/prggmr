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
 * \prggmr\Engine Unit Tests
 */

include_once 'bootstrap.php';

/**
 * Signal used for testing compare always returns signal
 */
class TestSignal extends \prggmr\Signal
{
    public function compare($signal)
    {
        return 'test';
    }

    public function canIndex()
    {
        return false;
    }
}

class EngineTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        $this->engine = new \prggmr\Engine();
    }

    public function tearDown()
    {
        $this->engine->flush();
    }
    
    public function assertEvent($event, $params, $expected)
    {
        $event = $this->engine->fire($event, $params);
        $this->assertInstanceOf('\prggmr\Event', $event);
        $this->assertEquals($expected, $event->getData());
    }

    /**
     * Methods Covered
     * @Engine\Subscribe
     *     @with option name
     * @Engine\hasSubscriber
     */
    public function testSubscribe()
    {
        $this->engine->subscribe('subscriber', function($event){}, 'testSubscribe');
        $this->assertTrue($this->engine->count() == 1);
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with single parameter injection
     */
    public function testEventSingleParameter()
    {
        $this->engine->subscribe('subscribe-parameter-single', function($event, $param1){
            $event->setData($param1);
        }, 'testEventSingleParameter');
        $this->assertEvent('subscribe-parameter-single', array('helloworld'), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with multiple parameter injection from regex
     */
    public function testEventWithMultipleParameter()
    {
        $this->engine->subscribe('multiparam', function($event, $param1, $param2){
            $event->setData($param1.$param2);
        }, 'testEventWithMultipleParameter');
        $this->assertEvent('multiparam', array('hello', 'world'), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with parameter injection from regex
     */
    public function testEventSingleRegexParameter()
    {
        $signal = new \prggmr\RegexSignal('regexparam/([a-z]+)');
        $this->engine->subscribe($signal, function($event, $param){
            $event->setData($param);
        }, 'testEventSingleRegexParameter');
        $this->assertEvent('regexparam/helloworld', array(), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with multiple parameter injection from regex
     */
    public function testEventWithMultipleRegexParameter()
    {
        $signal = new \prggmr\RegexSignal('multiregexparam/([a-z]+)/([a-z]+)');
        $this->engine->subscribe($signal, function($event, $param1, $param2){
            $event->setData($param1.$param2);
        }, 'testEventWithMultipleRegexParameter');
        $this->assertEvent('multiregexparam/hello/world', array(), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with multiple parameter injection from regex
     *      @with parameters supplied
     */
    public function testEventWithMultipleRegexAndMultipleSuppliedParamters()
    {
        $signal = new \prggmr\RegexSignal('multiparam2/([a-z]+)/([a-z]+)');
        $this->engine->subscribe($signal, function($event, $param1, $param2, $regex1, $regex2){
            $event->setData($param1.$param2.$regex1.$regex2);
        }, 'testEventWithMultipleRegexAndMultipleSuppliedParamters');
        $this->assertEvent('multiparam2/wor/ld', array('hel','lo'), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with simplified regex
     */
    public function testRegexEventWithSimpleRegex()
    {
        $this->engine->subscribe(new \prggmr\RegexSignal('simpleregex/:name'), function($event, $name){
            $event->setData($name);
        }, 'testRegexEventWithSimpleRegex');
        $this->assertEvent('simpleregex/helloworld', array(), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with multiple simplified regex
     */
    public function testEventWithMultipleSimpleRegex()
    {
        $this->engine->subscribe(new \prggmr\RegexSignal('multisimpleregex/:name/:slug'), function($event, $name, $slug){
            $event->setData($name.$slug);
        }, 'testEventWithMultipleSimpleRegex');
        $this->assertEvent('multisimpleregex/hello/world', array(), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with multiple simplified regex
     *      @with mutplie supplied parameters
     */
    public function testEventWithMultipleSimpleRegexAndSuppliedParameters()
    {
        $this->engine->subscribe(new \prggmr\RegexSignal('multisimpleregexparamsupplied/:name/:slug'), function($event, $param1, $param2, $name, $slug){
            $event->setData($name.$param1.$slug.$param2);
        }, 'testEventWithMultipleSimpleRegexAndSuppliedParameters');
        $this->assertEvent('multisimpleregexparamsupplied/hel/wor', array('lo','ld'), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with regex param
     *      @with simplified regex
     */
    public function testEventWithSimpleRegexAndRegexParameters()
    {
        $this->engine->subscribe(new \prggmr\RegexSignal('simpleandregex/:name/([a-z]+)'), function($event, $param1, $param2){
            $event->setData($param1.$param2);
        }, 'testEventWithSimpleRegexAndRegexParameters');
        $this->assertEvent('simpleandregex/hello/world', array(), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\Subscription
     *      @with regex event name
     *      @with object return false
     *      @with regex param
     *      @with simplified regex
     */
    public function testEventWithSimpleRegexRegexAndSuppliedParameters()
    {
        $this->engine->subscribe(new \prggmr\RegexSignal('simpleregexsupplied/:name/([a-z]+)'), function($event, $param1, $param2, $param3){
            $event->setData($param2.$param1.$param3);
        }, 'testEventWithSimpleRegexRegexAndSuppliedParameters');
        $this->assertEvent('simpleregexsupplied/hel/ld', array('lowor'), array('helloworld'));
    }

    /**
     * Methods Covered
     * @Engine\flush
     */
    public function testFlush()
    {
        $this->engine->subscribe('test', function(){});
        $this->assertTrue($this->engine->count() == 1);
        $this->engine->flush();
        $this->assertTrue($this->engine->count() == 0);
    }

    public function testEngineErrorState()
    {
        $this->engine->subscribe('stateerrortest', function($event){
            $event->setState(\prggmr\Event::STATE_ERROR);
        }, 'error_state_test');
        $this->engine->fire('stateerrortest');
		$sub = $this->engine->queue('stateerrortest', false)->locate('error_state_test');
		$this->assertFalse($sub);
    }

    /**
     * Methods Covered
     * @Engine\bubble
     *      @with event halt
     */
    public function testEventHalt()
    {
        $this->engine->subscribe('halt', function($event){
            $event->setData('Hello');
        });
        $this->engine->subscribe('halt', function(){
            return false;
        });
        $this->engine->subscribe('halt', function(){
            return 'World';
        });
        $this->assertEvent('halt', array(), array('Hello'));
    }

    /**
     * Test event chaining
     */
    public function testEventChain()
    {
        $this->engine->subscribe('test', function($event){
           $event->setData('one');
        }, null, null, 'chain_link_1');
        $this->engine->subscribe('chain_link_1', function($event){
            $event->setData('two');
        }, null, null, 'chain_link_2');
        $this->engine->subscribe('chain_link_2', function($event){
            $event->setData('three');
        });
        $event = $this->engine->fire('test');
        $this->assertEquals(array('one'), $event->getData());
        $link1 = $event->getChain();
        $this->assertType('array', $link1);
        $this->assertInstanceOf('\prggmr\Event', $link1[0]);
        $link2 = $link1[0]->getChain();
        $this->assertType('array', $link2);
        $this->assertInstanceOf('\prggmr\Event', $link2[0]);
        $this->assertEquals(array('two'), $link1[0]->getData());
        $this->assertEquals(array('three'), $link2[0]->getData());
    }

    public function testQueue()
    {
        $this->assertEquals(0, $this->engine->count());
        $this->engine->subscribe('test', function(){});
        $this->assertInstanceOf('\prggmr\Queue', $this->engine->queue('test'));
        $this->assertFalse($this->engine->queue('none', false));
        $class = new \stdClass();
        $queue = $this->engine->queue($class, true);
        $this->assertFalse((false === $this->engine->queue($class, false)));
    }

    public function testIdentifier()
    {
        $this->assertEquals(0, $this->engine->count());
        $this->engine->subscribe('test', function(){}, 'test_sub');
        $this->engine->subscribe('test', function(){}, 1);
        $this->engine->subscribe('test', function(){}, 1.25);
        $this->engine->subscribe('test', function(){}, false);
        $this->engine->subscribe('test', function(){}, true);
        $this->engine->subscribe('test', function(){}, null);
        $this->assertEquals(6, $this->engine->queue('test')->count());
        $this->assertTrue($this->engine->queue('test')->locate('test_sub'));
        $this->assertTrue($this->engine->queue('test')->locate(1.25));
        $this->assertTrue($this->engine->queue('test')->locate(1));
        $this->assertTrue($this->engine->queue('test')->locate(true));
        $this->assertTrue($this->engine->queue('test')->locate(null));
        $this->assertTrue($this->engine->queue('test')->locate(false));
    }

    public function testDequeue()
    {
        $this->assertEquals(0, $this->engine->count());
        $this->engine->subscribe('test', function(){}, 'test_sub');
        $this->engine->subscribe('test', function(){}, 'test_sub_1');
        $this->assertTrue($this->engine->queue('test')->locate('test_sub'));
        $this->assertTrue($this->engine->queue('test')->locate('test_sub_1'));
        $this->engine->dequeue('test', 'test_sub');
        $this->assertFalse($this->engine->queue('test')->locate('test_sub'));
        $this->assertTrue($this->engine->queue('test')->locate('test_sub_1'));
        $this->assertFalse($this->engine->dequeue(1, 'test'));
        $this->assertFalse($this->engine->dequeue(false, 'test', 'test'));
        $this->assertFalse($this->engine->dequeue(1.25, 'test'));
        $this->assertFalse($this->engine->dequeue(null, 'test'));
        $this->assertFalse($this->engine->dequeue(new stdClass(), 'test'));
        $this->assertFalse($this->engine->dequeue(true, 'test'));
    }

    public function testPriority()
    {
        $this->assertEquals(0, $this->engine->count());
        $this->engine->subscribe('testPriority', function($event){
            $event->setData('one');
        });
        $this->engine->subscribe('testPriority', function($event){
            $event->setData('two');
        }, null, 10);
        $this->engine->subscribe('testPriority', function($event){
           $event->setData('three');
        }, null, 1);
        $this->engine->subscribe('testPriority', function($event){
            $event->setData('four');
        }, null, '123');
        $this->engine->subscribe('testPriority', function($event){
            $event->setData('five');
        }, null, array('asd'));
        $this->assertEvent('testPriority', array(), array(
            'three', 'two', 'one', 'four', 'five'
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSubscription()
    {
        $this->engine->subscribe('test', 'asdf');
    }

    public function testfireEventParam()
    {
        $this->engine->subscribe('test', function($event){
            $event->setData('one');
        });
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), 'string')->getData());
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), 1)->getData());
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), null)->getData());
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), true)->getData());
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), false)->getData());
        $this->assertEquals(array('one'), $this->engine->fire('test', array(), 1.25)->getData());
    }

    public function testfireVarParam()
    {
        $this->engine->subscribe('test', function($event, $param){
            $event->setData($param);
        });
        $this->assertEquals(array(1.25), $this->engine->fire('test', 1.25)->getData());
        $this->assertEquals(array(1), $this->engine->fire('test', 1)->getData());
        $this->assertEquals(array('string'), $this->engine->fire('test', 'string')->getData());
        try {
            $this->assertEquals(array(true), $this->engine->fire('test', array())->getData());
        } catch (\RuntimeException $e) {
            $this->addToAssertionCount(1);
        }
        $this->assertEquals(array(true), $this->engine->fire('test', true)->getData());
        $this->assertEquals(array(false), $this->engine->fire('test', false)->getData());
        try {
            $this->assertEquals(array(), $this->engine->fire('test', null)->getData());
        } catch (\RuntimeException $e) {
            $this->addToAssertionCount(1);
        }
        $obj = new \stdClass();
        $this->assertEquals(array($obj), $this->engine->fire('test', $obj)->getData());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidEventOject()
    {
        $this->engine->subscribe('test', function($event){});
        $this->engine->fire('test', array(), new stdClass());
    }

    public function testcanIndex()
    {
        $this->assertTrue(\prggmr\Engine::canIndex(1));
        $this->assertTrue(\prggmr\Engine::canIndex('string'));
        $this->assertTrue(\prggmr\Engine::canIndex(new \prggmr\Signal('test')));
        $this->assertFalse(\prggmr\Engine::canIndex(1.0));
        $this->assertFalse(\prggmr\Engine::canIndex(new \stdClass()));
        $this->assertFalse(\prggmr\Engine::canIndex(true));
        $this->assertFalse(\prggmr\Engine::canIndex(false));
        $this->assertFalse(\prggmr\Engine::canIndex(array()));
    }

    public function testExhaustion()
    {
        $this->engine->subscribe('exhaust', function(){;}, 'exhaust_text', null, null, 5);
        $this->assertTrue($this->engine->queue('exhaust')->locate('exhaust_text'));
        for($i=0;$i!=5;$i++) {
            $this->engine->fire('exhaust');
        }
        $this->assertFalse($this->engine->queue('exhaust')->locate('exhaust_text'));
    }

    public function testTestSignal()
    {
        $signal = new TestSignal('test');
        $this->engine->subscribe($signal, function($event){ $event->setData('test'); });
        $results = $this->engine->fire('test');
        $this->assertEquals(array('test'), $results->getData());
    }

    public function testTimersAndDaemon()
    {
        $this->engine->flush();
        $count = 1;
		$this->engine->setTimeout(function($event, $unit){
			$unit->engine->clearInterval('intervalTest');
			$unit->addToAssertionCount(1);
		}, 5000, &$this, 'clearInterval');
        $this->engine->setInterval(function($event, $count, $unit) {
            echo ".";
            $count++;
            $unit->addToAssertionCount(1);
        }, 1000, array(&$count, &$this), 'intervalTest');
        $this->engine->setTimeout(function($event, $unit){
            $unit->engine->shutdown();
        }, 1000 * 7, &$this, 'shutdown');
        $this->engine->daemon();
        $this->assertEquals(\prggmr\Engine::SHUTDOWN, $this->engine->getState());
        $this->assertEquals(5, $count);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTimerInvalidCallback()
    {
        $this->engine->setInterval('test', 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTimersInvalidInterval()
    {
        $this->engine->setInterval(function(){;}, 0.0);
    }

	public function testEventHaltInDaemon()
	{
        $engine = new \prggmr\Engine();
		$count = 0;
		$engine->setTimeout(function($event, $engine){
			$engine->shutdown();
		}, 200, &$engine);
        $engine->setInterval(function($event, $count) {
            echo ".";
            $count++;
			if ($count == 2) {
				$event->halt();
			}
        }, 50, array(&$count), 'intervalTest');
		$engine->daemon();
		$this->assertEquals(2, $count);
	}

	public function testClearTimeout()
	{
		$count = 0;
		$this->engine->setTimeout(function($event) use (&$count){
			echo ".";
			$count++;
		}, 200, null, 'timeout_clear');
		$this->engine->clearTimeout('timeout_clear');
		$this->engine->daemon(false, 300);
		$this->assertEquals(0, $count);
	}
    
    public function testReturnEventData()
    {
        $this->engine->flush();
        $this->assertEquals(0, $this->engine->count());
        $this->engine->subscribe('test', function($event){
            return 'HelloWorld';  
        });
        $this->engine->fire('test');
        $this->assertEquals(array(
            'return' => 'HelloWorld'
        ), $this->engine->fire('test')->getData());
    }
}