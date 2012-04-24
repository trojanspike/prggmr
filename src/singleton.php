<?php
namespace prggmr;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Singleton trait used for making a singleton object.
 */
trait Singleton {

    /**
     * @var  object|null  Instanceof the singleton
     */
    private static $_instance = null;

    /**
     * By default constructing is disallowed and performs no logic.
     */
    protected function __construct(){}

    /**
     * Returns an instance of the singleton.
     */
    final public static function instance(/* ... */)
    {
        if (null === static::$_instance) {
            static::$_instance = new self();
        }

        return self::$_instance;
    }
}