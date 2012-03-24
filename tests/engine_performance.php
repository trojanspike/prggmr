<?php
require '../src/prggmr.php';
ini_set('memory_limit', -1);
echo memory_get_peak_usage().PHP_EOL;
$time = milliseconds();
$handle_count = 0;
$signal_count = 0;
$event = new \prggmr\Event();
$event->handle = 0;
for($i=0;$i!=1000;$i++) {
    for ($a = 0;$a!=10;$a++) {
        $handle_count++;
        handle(function(){
            $this->handle++;
        }, $i);
    }
    signal($i, null, $event);
}
echo 'Called '.$event->handle . ' handles'.PHP_EOL;
echo "Setup $handle_count handlers".PHP_EOL;
echo milliseconds() - $time;
echo PHP_EOL.memory_get_peak_usage();
