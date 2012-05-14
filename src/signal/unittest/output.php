<?php
namespace prggmr\signal\unittest;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Output colors
 */
if (!defined('OUTPUT_COLORS')) {
    define('OUTPUT_COLORS', false);
}

/**
 * Maximum depth to transverse a tree or object while outputing.
 */
if (!defined('MAX_DEPTH')) {
    define('MAX_DEPTH', 2);
}

/**
 * Use shorterned variables within the output
 */
if (!defined('SHORT_VARS')) {
    define('SHORT_VARS', false);
}

/**
 * Level of verbosity for output
 */
if (!defined('VERBOSITY_LEVEL')) {
    define('VERBOSITY_LEVEL', 3);
}

/**
 * Generates output for a unit test.
 */
class Output {

    use \prggmr\Singleton;

    /**
     * Max recursion depth.
     */
    protected $_maxdepth = MAX_DEPTH;

    /**
     * Line break count
     */
    static protected $_breakcount = 0;
    
    /**
     * Output message types.
     */
    const MESSAGE = 0xF00;
    const ERROR   = 0xF01;
    const DEBUG   = 0xF02;
    const SYSTEM  = 0xF03;
    
    /**
     * Outputs an assertion result.
     * 
     * @param  object  $event  Test event object
     * @param  string  $assertion  Name of the assertion
     * @param  array|null  $args  Array of arguments used during test
     * @param  null|boolean  $result  True-pass,False-fail,null-skipped
     * 
     * @return  void
     */
    public function assertion($test, $assertion, $args) 
    {
        switch (VERBOSITY_LEVEL) {
            case 3:
                $this->send(sprintf(
                    '%s %s Passed (%s)',
                    $test->get_signal()->info(),
                    $assertion,
                    $this->variable($args)
                ), self::SYSTEM);
                $this->send(sprintf(
                    "%s--------------------------------------------%s",
                    PHP_EOL, PHP_EOL
                ), self::SYSTEM);
                $this->send($this->get_assertion_call_line(), self::SYSTEM);
                break;
            case 2:
               $this->send(sprintf(
                    "%s%s",
                    $assertion,
                    PHP_EOL
                ), self::SYSTEM);
                break;
            default:
            case 1:
                $this->send(".", self::SYSTEM);
                break;
        }
    }
    
    /**
     * Sends a string to output.
     *
     * @param  string  $string  Message to output
     * @param  string  $type  Type of message
     * @param  boolean  $newline  Output line after string.
     *
     * @return  void
     */
    public static function send($string, $type = null, $newline = false)
    {
        $message = null;
        if (null === $type) {
            $type = self::MESSAGE;
        }
        switch ($type) {
            default:
            case self::MESSAGE:
                if (OUTPUT_COLORS) {
                    $message .= "\033[1;34m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (OUTPUT_COLORS) {
                    $message .= "\033[0m";
                }
                break;
            case self::ERROR:
                if (OUTPUT_COLORS) {
                    $message .= "\033[1;31m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (OUTPUT_COLORS) {
                    $message .= "\033[0m";
                }
                break;
            case self::DEBUG:
                if (OUTPUT_COLORS) {
                    $message .= "\033[1;33m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (OUTPUT_COLORS) {
                    $message .= "\033[0m";
                }
                break;
            case self::SYSTEM:
                if (OUTPUT_COLORS) {
                    $message .= "\033[1;36m";
                }
                $message .= sprintf("%s",
                    $string
                );
                if (OUTPUT_COLORS) {
                    $message .= "\033[0m";
                }
                break;
        }
        print($message);
        if ($newline) print PHP_EOL;
    }
    
    /**
     * Returns if short vars are enabled or to use.
     *
     * @param  string  $str 
     *
     * @return  boolean
     */
    public function use_short_vars($str = null)
    {
        return (null === $str) ? SHORT_VARS :
                (SHORT_VARS && is_string($str) && strlen($str) >= 60);
    }
    
    /**
     * Generates PHP vars like printr, var_dump the output is limited
     * by using shortvars and the maximum output length.
     *
     * Recursion is not checked for.
     *
     * @param  mixed  $v
     * @param  integer  $depth  Current transvering depth.
     *
     * @return  string  
     */
    public function variable($v, &$depth = 0)
    {
        switch ($v) {
            case is_bool($v):
                if ($v) {
                    return "bool(true)";
                }
                return "bool(false)";
                break;
            case is_null($v):
                if (false === $v) {
                    return "bool(false)";
                }
                return "null";
                break;
            case is_int($v):
            case is_float($v):
            case is_double($v):
            default:
                return sprintf('%s(%s)',
                    gettype($v),
                    $v);
                break;
            case is_string($v):
                return sprintf('string(%s)',
                    ($this->use_short_vars($v)) ? substr($v, 0, 60) : $v
                );
                break;
            case is_array($v):
                $r = array();
                foreach ($v as $_key => $_var) {
                    if ($depth >= $this->_maxdepth) break;
                    $depth++;
                    $r[] = sprintf('[%s] => %s',
                        $_key,
                        $this->variable($_var, $depth)
                    );
                }
                $return = sprintf('array(%s)', implode(", ", $r));
                return ($this->use_short_vars($return)) ? sprintf('%s...)',
                    substr($return, 0, 60)) : $return;
                break;
            case is_object($v):
                return sprintf('object(%s)', get_class($v));
            break;
        }
        
        return "unknown";
    }
    
    /**
     * Outputs a readable backtrace, by default it just dumps it from a for.
     * The output generator is at fault for providing it simplified.
     *
     * @param  array  $backtrace  debug_print_backtrace()
     *
     * @return  void
     */
    public function backtrace($backtrace, $type = self::ERROR)
    {
        $endtrace = '';
        for($a=0;$a!=count($backtrace);$a++) {
            if (isset($backtrace[$a]['file']) && isset($backtrace[$a]['line'])) {
                $endtrace .= sprintf("{%s} - %s %s %s\n",
                    $a,
                    $backtrace[$a]['file'],
                    $backtrace[$a]['line'],
                    $backtrace[$a]['function']
                );
            }
        }
        $this->send($endtrace, $type);
    }

    /**
     * Returns the location an assertion was called.
     * 
     * Currently this just returns the node at index 3 a more advanced method
     * for determaining the assertion call will need to be developed to 
     * account for changes in the flow.
     * 
     * @return  array
     */
    public function get_assertion_call_line()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[3];
        return sprintf("File: %s%sLine: %s%sFunction: %s%s",
            $trace['file'],
            PHP_EOL,
            $trace['line'],
            PHP_EOL,
            $trace['function'],
            PHP_EOL
        );
    }
}