<?php
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
 * @package  prggmr
 * @copyright  Copyright (c), 2010-12 Nickolas Whiting
 */

if (!class_exists('prggmr')) {
    require_once 'prggmr/lib/prggmr.php';
}

foreach (glob('../*.php') as $_file) {
    $_name = explode('/', $_file);
    $_class = array_map('ucfirst', explode('_', 
        str_replace('.php', '', end($_name))
    ));
    if (!class_exists(implode('', $_class))) {
        include_once $_file;
    }
}