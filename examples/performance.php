<?php
ini_set('memory_limit', -1);
$time = microtime(true);
for ($i=0;$i!=2000;$i++){
    prggmr\handle(function() use ($i){}, $i);
}
echo "Handle Register".PHP_EOL;
echo microtime(true) - $time;
echo PHP_EOL;
$time = microtime(true);
for ($i=0;$i!=2000;$i++){
    prggmr\signal($i);
}
echo "Signal Calls".PHP_EOL;
echo microtime(true) - $time;
