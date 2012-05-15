<?php
require '../src/prggmr.php';
/**
 * This must run directly in your browser!
 * 
 * Use the php built server:
 * php -S 127.0.0.1:5000 index.php
 */

/**
 * Load the http signal library
 */
prggmr\load_signal('http');

// Shorten namespace down
use prggmr\signal\http as http;
/**
 * Route to /
 */
prggmr\signal_interrupt(function(){
    $this->db = new stdClass();
    // echo "Here";
    return false;
}, 'prggmr\signal\http\Uri');

http\api\uri_request(function(){
    echo "Hello World";
    var_dump($this->db);
}, "/");

http\api\uri_request(function(){
    echo 1111;
},['/dashboard/:param', ['param' => '.*']]);

http\api\uri_request(function(){
    echo "At the dashboard";
}, '/dashboard');

/**
 * Route to /user/:name
 */
prggmr\signal_interrupt(function($name){
    echo "Performing pre-handle action on $name";
}, new http\Uri("/user/:name"), null, null, true);

http\api\uri_request(function($name, $dog){
    echo "WHAT $name $dog";
}, "/user/:name/:dog");

http\api\uri_request(function($name){
    echo "Hello $name";
}, "/user/:name");

/**
 * Route to /admin/:id
 * 
 * Cancel stack in interrupt if $_GET['auth'] is not set
 */
prggmr\signal_interrupt(function($id){
    if (!isset($_GET['auth'])) {
        echo "You do not have permission to view $id";
        $this->halt();
    } else {
        echo $_GET['auth'];
    }
}, new http\Uri("/admin/:id"));

http\api\uri_request(function($id){
    echo "Viewing $id";
}, "/admin/:id");

prggmr\loop();