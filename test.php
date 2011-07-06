<?php
require 'lib/prggmr.php';

//Prggmr::instance()->setTimeout(function(){
//
//    echo "This will shutdown the daemon in 10 seconds\n";
//
//    Prggmr::instance()->setInterval(function($event){
//        $data = $event->getData();
//        $time = (null == $data['time']) ? 10 : $data['time'];
//        if ($time === 1) {
//            Prggmr::instance()->clearInterval('testInterval');
//        }
//        echo "$time seconds till shutdown\n";
//        $event->setData($time - 1, 'time');
//    }, 1000, null, 'testInterval');
//
//    Prggmr::instance()->setTimeout(function(){
//        //pcntl_fork();
//        echo "Shutting down the daemon\n";
//        Prggmr::instance()->shutdown();
//    }, 1000 * 11);
//
//}, 1000);
//
//echo "Starting prggmr daemon\n";
//
//Prggmr::instance()->daemon(true);
//
//echo "Daemon has stopped!\n";
//
//class prggmrException {}
//
//throw new prggmrException();