<?php
/**
 * This demonstrates prggmr's ability to perform CPE using the example 
 * taking from Wikipedia of a wedding taking place.
 */
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
            return [null, ENGINE_ROUTINE_SIGNAL, null];
        }

        return false;
    }

}

// signal handlers

// When the wedding takes places
prggmr\handle(function(){
    echo "A wedding is taking place!".PHP_EOL;
    // A little later the wedding ends
    prggmr\timeout(function(){
        prggmr\signal('wedding_over');
    }, 10000);
}, new Wedding());

// When the man arrives
prggmr\handle(function(){
    echo "The man has arrived".PHP_EOL;
}, 'man');

// When the woman arrives
prggmr\handle(function(){
    echo "The woman has arrived".PHP_EOL;
}, 'woman');

// When the bells ring
prggmr\handle(function(){
    echo "Wedding bells Ringing".PHP_EOL;
}, "bells");

// When the wedding is over
prggmr\handle(function(){
    echo "The wedding is over".PHP_EOL;
}, 'wedding_over');

// man arrives late because of second thoughts
prggmr\timeout(function(){
    prggmr\signal('man');
}, 5000);

// couple seconds after he arrives the bells start ringing
prggmr\timeout(function(){
    prggmr\signal('bells');
}, 10000);

// Woman is ready first
prggmr\signal('woman');