<?php
setTimeout(function($event){
    echo "A second just passed!";
    // shutdown
    shutdown();
}, 1000);