<?php
ini_set('memory_limit', -1);
$handle_count = 0;
$signal_count = 0;
$event = new \prggmr\Event();
$event->handle = 0;
echo memory_get_usage().PHP_EOL;
$time = microtime(true);
for($i=0;$i!=100;$i++) {
    for ($a = 0;$a!=10;$a++) {
        $handle_count++;
        handle(function(){
            $this->handle++;
        }, $i, 0, null);
    }
    // signal($i, null, $event);
}
echo "Setup $handle_count handles".PHP_EOL;
echo "Handled ".$event->handle." times".PHP_EOL;
echo microtime(true) - $time;
echo PHP_EOL.memory_get_peak_usage().PHP_EOL;