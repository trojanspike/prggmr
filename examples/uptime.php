<?php
/**
 * Runs a status check every 5 minutes to ensure a website is up
 * keeping track of its uptime.
 * If a check fails it goes into a failure state checking every 30
 * seconds until it recieves a postive response or fails 5 times at
 * which point it will send a notification things are down and start
 * the process over again.
 */
require '../lib/prggmr.php';
$engine = new \prggmr\Engine();

// failure event
$engine->subscribe('failure', function($event){
    // uptime reset
    $event->uptime = 0;
    echo sprintf(
        "Server was up a total of %d minutes\n",
        $event->uptime / 60
    );
    // send out an email on failure
    echo "NOTIFY!\n";
    mail('your@email.com', 'PING FAILURE', sprintf(
        'Website ping failure - %s',
        date('m/d/y h:ia')
    ));
});

// cURL ping check
function ping() {
    $ping = curl_init('google.com');
    curl_setopt($ping, CURLOPT_NOBODY, true);
    curl_setopt($ping, CURLOPT_FOLLOWLOCATION, true);
    // timeout after 10 seconds
    curl_setopt($ping, CURLOPT_CONNECTTIMEOUT, 10);
    curl_exec($ping);
    $code = curl_getinfo($ping, CURLINFO_HTTP_CODE);
    curl_close($ping);
    return $code;
}

// ping every 5 minutes if failure ping again in 30 seconds intervals
// once there are 5 failures notify
// if success clear check reset counter
$engine->setInterval(function($event) use ($engine){
    if (200 !== ping()) {
        if (!$event->fail) $event->fail = 0;
        echo "FAILURE FOUND START FAILSAFE ----\n";
        $engine->setInterval(function($event) use ($engine){
            if (200 !== ping()) {
                echo "FAILURE\n";
                if ($event->fail >= 5) {
                    $event->fail = 0;
                    $engine->clearInterval('fail-check');
                    $engine->fire('failure', null, $event);
                    // return false to break event
                    return false;
                }
                $event->fail++;
            } else {
                $event->fail = 0;
                // drop this fail check
                $engine->clearInterval('fail-check');
            }
        }, 1000 * 30, array($event), 'fail-check');
    } else {
        if (!$event->uptime) {
            $event->uptime = 0;
        }
        // uptime tracked in seconds
        $event->uptime += 60 * 5;
        echo sprintf(
            "Server has been up for %d minutes\n",
            $event->uptime / 60
        );
    }
}, (1000 * (60 * 5)));

// run the daemon
$engine->daemon();