<?php


handle(function(){
    signal('b');
}, 'a');

handle(function(){
    signal('c');
}, 'a', 0);

handle(function(){
    signal('c');
}, 'b');

handle(function(){
    signal('d');
}, 'c');

handle(function(){
    echo 1;
}, 'd');

signal('a');

var_dump(event_history());