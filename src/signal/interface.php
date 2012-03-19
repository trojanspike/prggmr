<?php
namespace prggmr\signal;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Signal Interface.
 */
interface SignalInterface {

    /**
     * Compares the event signal given aganist itself.
     *
     * @param  mixed  $signal  Signal to compare
     *
     * @return  mixed  False on failure. True if matches. String/Array
     *          return results found via the match.
     */
    public function evaluate($var);

    /**
     * Returns if this signal returns an indexable value.
     *
     * @return  boolean
     */
    public function getSignal(/* ... */);
}