<?php
ini_set('memory_limit', -1);
$handle_count = 0;
$signal_count = 0;
handle(function(){
}, 'a', null, null);
function a() {
}
$time = microtime(true);
$mem = memory_get_usage();
for($i=0;$i!=2000;$i++) {
    a();
}
$end = memory_get_usage();
$t1 = microtime(true) - $time;
echo "Calling a function".PHP_EOL;
echo $t1.PHP_EOL;
echo $end - $mem . PHP_EOL;
$time = null;
$time = microtime(true);
$mem = memory_get_usage();
for($i=0;$i!=2000;$i++) {
    signal('a');
}
$end = memory_get_usage();
echo "Calling a signal".PHP_EOL;
$t2 = microtime(true) - $time;
echo $t2.PHP_EOL;
echo $end - $mem . PHP_EOL;
if ($t2 >= $t1) {
    echo "Function was faster".PHP_EOL;
    echo $t2 - $t1;
} else {
    echo "Signal was faster".PHP_EOL;
    echo $t1 - $t2;
}
echo PHP_EOL;
// var_dump(prggmr::instance());