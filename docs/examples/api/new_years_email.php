<?php
// always make sure!
date_default_timezone_set('America/New_York');
$time = 10;
setInterval(function() use (&$time){
    if ($time != 0) {
        $message = sprintf(
            "%s seconds till the new year begins!",
            $time
        );
    } else {
        $message = sprintf(
            "HAPPY HAPPY NEW YEAR FROM prggmr IT'S %s",
            date('Y')
        );
        clearInterval('New Years Celebration Email');
    }
    mail(
        'your@email.com',
        'HAPPY NEW YEAR!', 
        sprintf(
            "HAPPY HAPPY NEW YEAR FROM prggmr IT'S %s",
            date('Y')
        )
    );
    $time--;
}, 1000, null, 'New Years Celebration Email', '12-31-'.date('Y').' 23:59:50'); 