<?php

/**
 * You write classes with each method as the name of the
 * signal that will dispatch.
 */
class User extends prggmr\EventHandler {

    /**
     * Handle the some_signal signal.
     * 
     * @return  boolean
     */
    public function register_user() 
    {
        echo "This would be called on the register_user signal.";
        return true;
    }

    /**
     * Something else happens here.
     *
     * @return  boolean
     */
    public function user_login()
    {
        echo "This would be called on the user_register signal.";
    }
}

/**
 * Register the object as a handle itself.
 */
prggmr\handle(new User());

/**
 * Signals would be as usual
 */
// This would call the User->register_user method
prggmr\signal('register_user');
// This would call the User->user_login method
prggmr\signal('user_login');
