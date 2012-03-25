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

handle(function(){
    echo "The man has arrived".PHP_EOL;
}, 'man');

handle(function(){
    echo "The woman has arrived".PHP_EOL;
}, 'woman');

handle(function(){
    echo "Wedding bells Ringing".PHP_EOL;
}, "bells");

handle(function(){
    echo "A wedding is taking place!";
    exit();
}, new Wedding());

signal('man');
signal('woman');
signal('bells');

prggmr_loop();