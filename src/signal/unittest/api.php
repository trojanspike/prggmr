<?php
namespace prggmr\signal\unittest\api;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * API can be included to load the entire signal.
 */

use \prggmr\signal\unittest as unittest;

/**
 * Add a new assertion function.
 * 
 * @param  closure  $function  Assertion function
 * @param  string  $name  Assertion name
 * @param  string  $message  Message to return on failure.
 * 
 * @return  void
 */
function create_assertion($function, $name, $message = null) {
    return unittest\Assertions::instance()->create_assertion($function, $name, $message);
}

/**
 * Creates a new test case.
 * 
 * @param  object  $function  Test function
 * @param  string  $name  Test name
 * @param  object  $event  prggmr\signal\unittest\Event
 * 
 * @return  array  [Handle, Signal]
 */
function test($function, $name = null, $event = null) {
    $signal = new unittest\Test($name, $event);
    $handle = \prggmr\handle($function, $signal);
    return [$handle, $signal];
}

/**
 * Constructs a new unit testing suite.
 * 
 * @param  object  $function  Closure
 * @param  object|null  $event  prggmr\signal\unittest\Event
 * 
 * @return  void
 */
function suite($function, $event = null) {
    return new unittest\Suite($function, \prggmr\prggmr(), $event);
}

/**
 * Registers a standard output mechanism for test results.
 * 
 * @return  void
 */
function generate_output() {
    // Startup
    \prggmr\handle(function(){
        $output = unittest\Output::instance();
        $output->send("prggmr unittest library " . PRGGMR_VERSION, 
            unittest\Output::SYSTEM
        );
        $output->send_linebreak(unittest\Output::SYSTEM);
    }, \prggmr\engine\Signals::LOOP_START);

    // Shutdown
    \prggmr\handle(function(){

        $tests = 0;
        $pass = 0;
        $fail = 0;
        $skip = 0;
        $output = unittest\Output::instance();
        foreach (\prggmr\event_history() as $_node) {
            if ($_node[0] instanceof unittest\Event) {
                $tests++;
                $failures = [];
                // Get passed
                foreach ($_node[0]->get_assertion_results() as $_assertion) {
                    if ($_assertion[0] === true) {
                        $pass++;
                    } elseif ($_assertion[0] === null) {
                        $skip++;
                    } else {
                        $fail++;
                        $failures[] = $_assertion;
                    }
                }

                if (count($failures) != 0) {
                    $output->send_linebreak(unittest\Output::ERROR);
                    $output->send(sprintf(
                        "%s had failures", 
                        $_node[1]->info()
                    ), unittest\Output::ERROR);
                    $output->send_linebreak(unittest\Output::ERROR);
                    foreach ($failures as $_failure) {
                        $output->send($_failure[0], unittest\Output::ERROR, true);
                        $output->send(sprintf(
                            'ARGS : %s',
                            $output->variable($_failure[2])
                        ), unittest\Output::ERROR);
                        $output->send_linebreak(unittest\Output::ERROR);
                    }
                }
            }
        }

        $output->send_linebreak();
        $output->send("Ran $tests tests", unittest\Output::SYSTEM, true);

        $output->send(sprintf("%s Assertions Run. %s Passed, %s Failed, %s Skipped",
            $pass + $fail + $skip,
            $pass, $fail, $skip
        ), unittest\Output::SYSTEM, true);

    }, \prggmr\engine\Signals::LOOP_SHUTDOWN);
}