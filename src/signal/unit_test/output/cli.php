<?php
namespace prggmr\signal\unit_test\output;
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
 * @package  prggmrunit
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
 */

/**
 * Generates output for the command line.
 */
class CLI extends unit\Output {
    
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
     * Compiles the CLI output generator.
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
    public function assertion_pass($event, $assertion, $args) 
    {           
        switch (CLI::$verbosity) {
            case 3:
                CLI::send(sprintf(
                    '%s %s Passed with args %s',
                    $test->getSubscription()->getIdentifier(),
                    sprintf('%s\%s', $namespace, $assertion),
                    \prggmrunit\Output::variable(array_slice($args, 1, count($args) - 1))
                ), CLI::SYSTEM);
                CLI::send(sprintf(
                    "%s--------------------------------------------%s",
                    PHP_EOL, PHP_EOL
                ), CLI::SYSTEM);

                break;
            case 2:
                CLI::send(sprintf(
                    "%s Passed%s",
                    $assertion,
                    PHP_EOL
                ), CLI::SYSTEM);
                break;
            default:
            case 1:
                CLI::send(".", CLI::SYSTEM);
                break;
        }
    }
        
        // Assertion Fail
        \prggmrunit::instance()->subscribe(
            \prggmrunit\Events::TEST_ASSERTION_FAIL, function($test, $assertion, $args, $namespace, $test){
                switch (CLI::$verbosity) {
                    case 3:
                        CLI::send(sprintf(
                            '%s %s Failed with args %s',
                            $test->getSubscription()->getIdentifier(),
                            sprintf('%s\%s', $namespace, $assertion),
                            \prggmrunit\Output::variable(array_slice($args, 1, count($args) - 1))
                        ), CLI::ERROR);
                        CLI::send(sprintf(
                            "%s--------------------------------------------%s",
                            PHP_EOL, PHP_EOL
                        ), CLI::ERROR);

                        break;
                    case 2:
                        CLI::send(sprintf(
                            "%s Failed%s",
                            $assertion,
                            PHP_EOL
                        ), CLI::ERROR);
                        break;
                    default:
                    case 1:
                        CLI::send("F", CLI::ERROR);
                        break;
                }
        });
        
        // Assertion Skip
        \prggmrunit::instance()->subscribe(
            \prggmrunit\Events::TEST_ASSERTION_SKIP, function($test, $assertion, $args, $namespace, $test){
                switch (CLI::$verbosity) {
                    case 3:
                        CLI::send(sprintf(
                            '%s %s Skipped with args %s',
                            $test->getSubscription()->getIdentifier(),
                            sprintf('%s\%s', $namespace, $assertion),
                            \prggmrunit\Output::variable(array_slice($args, 1, count($args) - 1))
                        ), CLI::DEBUG);
                        CLI::send(sprintf(
                            "%s--------------------------------------------%s",
                            PHP_EOL, PHP_EOL
                        ), CLI::DEBUG);

                        break;
                    case 2:
                        CLI::send(sprintf(
                            "%s Skipped%s",
                            $assertion,
                            PHP_EOL
                        ), CLI::DEBUG);
                        break;
                    default:
                    case 1:
                        CLI::send("S", CLI::DEBUG);
                        break;
                }
        });
        
        /**
         * Provide a line break every 60 assertions.
         */
         $break = 0;
        \prggmrunit::instance()->subscribe(new \prggmr\ArrayContainsSignal(array(
            \prggmrunit\Events::TEST_ASSERTION_PASS,
            \prggmrunit\Events::TEST_ASSERTION_FAIL,
            \prggmrunit\Events::TEST_ASSERTION_SKIP
        )), function($event) use (&$break){
                $break++;
                if ($break == 60) {
                    $break = 0;
                    switch (static::$verbosity) {
                        case 3:
                        case 2:
                            CLI::send("60 Assertions have ran", CLI::SYSTEM);
                            CLI::send(PHP_EOL);
                            break;
                        case 1:
                        default:
                            CLI::send(" [ 60 ]", CLI::SYSTEM);
                            CLI::send(PHP_EOL);
                            break;
                    }
                }
        });
        
        // Testing is finished
        \prggmrunit::instance()->subscribe(\prggmrunit\Events::END, function($event, $engine){
            
            $results = $engine->getResults();
            
            if (0 != count($results->getMessages())) {
                CLI::send(sprintf(
                    "%s%s====================================================",
                    PHP_EOL, PHP_EOL
                ), CLI::MESSAGE);
                CLI::send(sprintf(
                    "%sTesting Messages%s",
                    PHP_EOL,
                    PHP_EOL
                ), CLI::MESSAGE);
                foreach ($results->getMessages() as $_type => $_messages) {
                    foreach ($_messages as $_message) {
                        foreach ($_message as $_fail) {
                            CLI::send(sprintf(
                                "%s--------------------------------------------%s",
                                PHP_EOL, PHP_EOL
                            ), $_type);
                            CLI::send(sprintf(
                                "File : %s %s",
                                $_fail['data'][0]['file'],
                                PHP_EOL
                            ), $_type);
                            CLI::send(sprintf(
                                "Line : %s%sMessage : %s%s%s",
                                $_fail['data'][0]['line'],
                                PHP_EOL,
                                $_fail['message'],
                                PHP_EOL, PHP_EOL
                            ), $_type);
                        }
                    }
                }
            }
            
            $size = function($size) {
                /**
                 * This was authored by another individual
                 */
                $filesizename = array(
                    " Bytes", " KB", " MB", " GB", 
                    " TB", " PB", " EB", " ZB", " YB"
                );
                return $size ? round(
                    $size/pow(1024, ($i = floor(log($size, 1024)))), 2
                ) . $filesizename[$i] : '0 Bytes';
            };

            
            CLI::send(sprintf(
                "%s===================================================%s",
                PHP_EOL, PHP_EOL
            ), CLI::MESSAGE);
            CLI::send(sprintf(
                "%s tests %s suites - %s seconds - %s%s%s",
                $results->getTestsTotal(),
                $results->getSuitesTotal(),
                $results->getRuntime(),
                $size($results->getMemoryUsage(), 4),
                PHP_EOL, PHP_EOL
            ), CLI::MESSAGE);
            if ($results->getFailedTests() != 0) {
                CLI::send(sprintf(
                    "FAIL (failures=%s, success=%s, skipped=%s)",
                    $results->getFailedTests(), 
                    $results->getPassedTests(), 
                    $results->getSkippedTests()
                ), CLI::ERROR);
            } else {
                CLI::send(sprintf(
                    "PASS (success=%s, skipped=%s)",
                    $results->getPassedTests(), 
                    $results->getSkippedTests()
                ), CLI::SYSTEM);
            }
            CLI::send(sprintf(
                "%sAssertions (pass=%s, fail=%s, skip=%s)%s",
                PHP_EOL, 
                $results->getPassedAssertions(), 
                $results->getFailedAssertions(), 
                $results->getSkippedAssertions()
                ,
                PHP_EOL
            ), CLI::MESSAGE);
        }, "CLI Test Output");
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
            case unit\Output::MESSAGE:
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
            case unit\Output::ERROR:
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
            case unit\Output::DEBUG:
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
            case unit\Output::SYSTEM:
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
