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
            return [ENGINE_ROUTINE_SIGNAL, null];
        }

        return false;
    }

}

// signal handlers

// wedding
handle(function(){
    echo "A wedding is taking place!".PHP_EOL;
    timeout(function(){
        signal('wedding_over');
    }, 10000);
}, new Wedding());

// man
handle(function(){
    echo "The man has arrived".PHP_EOL;
}, 'man');

// woman
handle(function(){
    echo "The woman has arrived".PHP_EOL;
}, 'woman');

// wedding bells
handle(function(){
    echo "Wedding bells Ringing".PHP_EOL;
}, "bells");

// wedding over
handle(function(){
    echo "The wedding is over".PHP_EOL;
}, 'wedding_over');

// man arrives late because of second thoughts
timeout(function(){
    signal('man');
}, 5000);

timeout(function(){
    signal('bells');
}, 10000);

// man is ready first
signal('woman');

prggmr_loop();