<?php
require '../src/prggmr.php';

class Wedding extends \prggmr\signal\Complex {

    protected $_signals = array(
        'man',
        'woman',
        'bells'
    );

    public function routine($history) 
    {
        $man = false;
        $woman = false;
        $bells = false;
        foreach ($history as $_node) {
            if ($node[1] instanceof \prggmr\signal\Complex) continue;
            if ($_node[1] instanceof \prggmr\Signal) {
                $sig = $_node[1]->info();
            } else {
                $sig = $_node[1];
            }
            if (in_array($sig, $this->_signals)) {
                switch($sig) {
                    case 'man':
                        $man = true;
                        break;
                    case 'woman':
                        $woman = true;
                        break;
                    case 'bells':
                        $bells = true;
                        break;
                }
            }
        }
        if ($man && $woman && $bells) {
            return ENGINE_ROUTINE_SIGNAL;
        }

        return false;
    }

}

// signal handlers
handle(function(){
    echo "The man has arrived".PHP_EOL;
    echo "Waiting for the woman".PHP_EOL;
}, 'man');

handle(function(){
    echo "The woman has arrived".PHP_EOL;
    echo "The wedding will start in 3 seconds".PHP_EOL;
    timeout(function(){
        signal('bells');
    }, 3000);
}, 'woman');

handle(function(){
    echo "Wedding bells Ringing".PHP_EOL;
}, "bells");

handle(function(){
    echo "A wedding is taking place!".PHP_EOL;
    timeout(function(){
        signal('wedding_over');
    }, 10000);
}, new Wedding());

handle(function(){
    echo "The wedding is over".PHP_EOL;
}, 'wedding_over');

timeout(function(){
    signal('woman');
}, 5000);

signal('man');

prggmr_loop();