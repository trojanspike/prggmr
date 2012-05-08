<?php
namespace prggmr\signal\unittest;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * A test suite.
 * 
 * The suite is designed to run a group of tests together.
 */
class Suite {

    /**
     * Event used in the suite.
     * 
     * @var  object  \prggmr\signal\unittest
     */
    protected $_event = null;

    /**
     * Engine in use.
     * 
     * @var  object  \prggmr\Engine
     */
    protected $_engine = null;

    /**
     * Setup function.
     * 
     * @var  object  \Closure
     */
    protected $_setup = null;

    /**
     * Teardown function
     * 
     * @var  object  Closure
     */
    protected $_teardown = null;

    /**
     * Constructs a new unit testing suite.
     * 
     * @param  object  $function  Closure
     * @param  object  $engine  prggmr\Engine
     * @param  object|null  $event  prggmr\signal\unittest\Event
     * 
     * @return  void
     */
    public function __construct($function, $engine, $event = null)
    {
        if (!$function instanceof \Closure) {
            throw new \InvalidArgumentException(
                "Suite requires instance of a Closure"
            );
        }
        if (!$engine instanceof \prggmr\Engine) {
            throw new \InvalidArgumentException(
                "Suite requires instance of a prggmr\Engine"
            );
        }
        $this->_engine = $engine;
        if (null === $event || !$event instanceof \prggmr\signal\unitest\Event) {
            $this->_event = new Event();
        }
        $function = $function->bindTo($this);
        $function();
    }

    /**
     * Registers the setup function.
     * 
     * @param  object  $function  Closure
     * 
     * @return  void
     */
    public function setup($function)
    {
        if (!$function instanceof \Closure) {
            throw new \InvalidArgumentException(
                "Suite requires instance of a Closure"
            );
        }
        $this->_setup = $function->bindTo($this->_event);
    }

    /**
     * Registers the teardown function.
     * 
     * @param  object  $function  Closure
     * 
     * @return  void
     */
    public function teardown($function)
    {
        if (!$function instanceof \Closure) {
            throw new \InvalidArgumentException(
                "Suite requires instance of a Closure"
            );
        }
        $this->_teardown = $function->bindTo($this->_event);
    }

    /**
     * Creates a new test case in the suite.
     * 
     * @param  object  $function  Test function
     * @param  string  $name  Test name
     */
    function test($function, $name = null) {
        $signal = new Test($name, $this->_event);
        $handle = $this->_engine->handle($function, $signal);
        if (null !== $this->_setup) {
            $this->_engine->signal_interrupt(
                $this->_setup, $signal, \prggmr\Engine::INTERRUPT_PRE
            );
        }
        if (null !== $this->_teardown) {
            $this->_engine->signal_interrupt(
                $this->_teardown, $signal, \prggmr\Engine::INTERRUPT_POST
            );
        }
        return [$signal, $handle];
    }
}