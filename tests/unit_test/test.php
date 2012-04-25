<?php
prggmr\load_signal('unit_test');

prggmr\signal\unit_test\api\test(function(){
    $this->true(true);
    $this->true(false);
    $this->true(true);
    $this->true(true);
    $this->true(true);
    $this->true(true);
});

prggmr\signal\unit_test\api\test(function(){
    $this->object(new \stdClass());
});

prggmr\handle(function(){
    $tests = 0;
    foreach (prggmr\event_history() as $_node) {
        if ($_node[0] instanceof prggmr\signal\unit_test\Event) {
            $tests++;
        }
    }
    echo "Ran $tests tests";
}, prggmr\engine\Signals::LOOP_SHUTDOWN);
