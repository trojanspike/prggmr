<?php
namespace prggmr\signal\unit_test\output;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

use prggmr\signal\unit_test as unit_test;

/**
 * Generates output for the command line.
 */
class Cli {
    
    /**
     * Output using colors.
     *
     * @var  boolean
     */
    static protected $_colors = false;
    
    /**
     * Verbose Level
     * 
     * @var  integer
     */
    static public $verbosity = 1;
    
    /**
     * Compiles the Cli output generator.
     *
     * Setup colors, our startup events, assertion printouts and end action.
     *
     * @return  void
     */
    public function __construct(/* ... */)
    {
        // check for colors
        if (defined('PRGGMR_UNIT_TEST_OUTPUT_COLORS')) {
            if (PRGGMR_UNIT_TEST_OUTPUT_COLORS === true) {
                static::$_colors = true;
            }
        }
    }
    
    /**
     * Output a assertion pass
     * 
     * @param  object  $event  Test event object
     * @param  string  $assertion  Name of the assertion
     * @param  array|null  $args  Array of arguments used during test
     * 
     * @return  void
     */
    public function assertion_pass($test, $assertion, $args) 
    {           
        switch (Cli::$verbosity) {
            case 3:
                Cli::send(sprintf(
                    '%s %s Passed with args %s',
                    $test->get_signal()->get_info(),
                    $assertion,
                    unit_test\Output::variable($args)
                ), unit_test\Output::SYSTEM);
                Cli::send(sprintf(
                    "%s--------------------------------------------%s",
                    PHP_EOL, PHP_EOL
                ), unit_test\Output::SYSTEM);

                break;
            case 2:
                Cli::send(sprintf(
                    "%s Passed%s",
                    $assertion,
                    PHP_EOL
                ), unit_test\Output::SYSTEM);
                break;
            default:
            case 1:
                Cli::send(".", Cli::SYSTEM);
                break;
        }
    }
     
    public function assertion_fail($test, $assertion, $args)
    {
        switch (Cli::$verbosity) {
            case 3:
                Cli::send(sprintf(
                    '%s %s Failed with args %s',
                    $test->get_signal()->get_info(),
                    $assertion,
                    unit_test\Output::variable($args)
                ), unit_test\Output::ERROR);
                Cli::send(sprintf(
                    "%s--------------------------------------------%s",
                    PHP_EOL, PHP_EOL
                ), unit_test\Output::ERROR);

                break;
            case 2:
                Cli::send(sprintf(
                    "%s Failed%s",
                    $assertion,
                    PHP_EOL
                ), unit_test\Output::ERROR);
                break;
            default:
            case 1:
                Cli::send("F", Cli::ERROR);
                break;
        }
    }
    
    public function assertion_skip($test, $assertion, $args) 
    { 
        switch (Cli::$verbosity) {
            case 3:
                Cli::send(sprintf(
                    '%s %s Skipped with args %s',
                    $test->get_signal()->get_info(),
                    $assertion,
                    unit_test\Output::variable($args)
                ), unit_test\Output::DEBUG);
                Cli::send(sprintf(
                    "%s--------------------------------------------%s",
                    PHP_EOL, PHP_EOL
                ), unit_test\Output::DEBUG);

                break;
            case 2:
                Cli::send(sprintf(
                    "%s Skipped%s",
                    $assertion,
                    PHP_EOL
                ), unit_test\Output::DEBUG);
                break;
            default:
            case 1:
                unit_test\Output::send("S", Cli::DEBUG);
                break;
        }
    }
    
    /**
     * Sends a string to output.
     *
     * @param  string  $string
     * @param  string  $type  
     *
     * @return  void
     */
    public static function send($string, $type = null)
    {
        $message = null;
        switch ($type) {
            default:
            case unit_test\Output::MESSAGE:
                if (static::$_colors) {
                    $message .= "\033[1;34m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (static::$_colors) {
                    $message .= "\033[0m";
                }
                break;
            case unit_test\Output::ERROR:
                if (static::$_colors) {
                    $message .= "\033[1;31m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (static::$_colors) {
                    $message .= "\033[0m";
                }
                break;
            case unit_test\Output::DEBUG:
                if (static::$_colors) {
                    $message .= "\033[1;33m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (static::$_colors) {
                    $message .= "\033[0m";
                }
                break;
            case unit_test\Output::SYSTEM:
                if (static::$_colors) {
                    $message .= "\033[1;36m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (static::$_colors) {
                    $message .= "\033[0m";
                }
                break;
        }
        print($message);
    }
}
