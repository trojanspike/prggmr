<?php
// Subscribe
subscribe(function($event, $user, $action){
    echo "Hello $user, Are you sure you want to $action?";
}, "UserAction", "Subscriptions with parameters");

// Fire
fire("UserAction", array(
    'Joe',
    'Delete the entire database'
));

// shutdown prggmr
shutdown();