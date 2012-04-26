<?php
ini_set('memory_limit', -1);
// $handle_count = 0;
// $signal_count = 0;
// prggmr\handle(function(){
// }, 'a', null, null);
// function a() {
// }
// $time = microtime(true);
// $mem = memory_get_usage();
// for($i=0;$i!=100000;$i++) {
//     a();
// }
// $end = memory_get_usage();
// $t1 = microtime(true) - $time;
// echo "Calling a function".PHP_EOL;
// echo $t1.PHP_EOL;
// echo $end - $mem . PHP_EOL;
// $time = null;
// $time = microtime(true);
// $mem = memory_get_usage();
// for($i=0;$i!=100000;$i++) {
//     prggmr\signal('a');
// }
// $end = memory_get_usage();
// echo "Calling a signal".PHP_EOL;
// $t2 = microtime(true) - $time;
// echo $t2.PHP_EOL;
// echo $end - $mem . PHP_EOL;
// if ($t2 >= $t1) {
//     echo "Function was faster".PHP_EOL;
//     echo $t2 - $t1;
// } else {
//     echo "Signal was faster".PHP_EOL;
//     echo $t1 - $t2;
// }
// echo PHP_EOL;
// var_dump(prggmr::instance());
// $time = microtime(true);
// for ($i=0;$i!=10000;$i++){
//     //prggmr\handle(function(){}, $i);
// }
// echo "Handle Register".PHP_EOL;
// echo microtime(true) - $time;
// echo PHP_EOL;
// $time = microtime(true);
// for ($i=0;$i!=10000;$i++){
//     prggmr\signal($i);
// }
// echo "Signal Calls".PHP_EOL;
// echo microtime(true) - $time;
// echo PHP_EOL;
prggmr\handle(function(){}, 'test');
prggmr\handle(function(){}, 'test');
prggmr\signal('test');
var_dump(prggmr::instance());