<?php
require '../lib/prggmr.php';
ini_set('memory_limit', '500M');
$microtime = function() {
	$time = explode(" ", microtime());
	return $time[0] + $time[1];
};
$length = 1; // number of seconds to run
$engine = new \prggmr\Engine();
$sub = 0;
$true = true;
$start = $microtime();
while($true) {
	if (($microtime() - $start) >= $length) {
		$end = $microtime();
		$true = false;
	}
	$engine->subscribe($sub, function(){;});
	$sub++;
}
echo "\n---------\n";
echo "Subscription Benchmark";
echo "\n---------\n";
echo "Test Length : ".($end - $start)." seconds\n";
echo "\n---------\n";
echo "Total Subscriptions : ".number_format($sub)."\n";
echo "\n--------------\nFLUSHING ENGINE\n--------------\n";

$engine->flush();

$fire = 0;
$true = true;
$start = $microtime();
while($true) {
	if (($microtime() - $start) >= $length) {
		$end = $microtime();
		$true = false;
	}
	$engine->fire($fire);
	$fire++;
}
echo "\n---------\n";
echo "Fire Benchmark";
echo "\n---------\n";
echo "Test Length : ".($end - $start)." seconds\n";
echo "\n---------\n";
echo "Total Fires : ".number_format($fire)."";
echo "\n---------\n";
