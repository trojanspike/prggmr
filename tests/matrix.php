<?php
// Creates a matrix style rain!
// First define our character set
#$range = str_split(',./;[]\1234568790-=`!~@#$%&^*()_+{}|:"<>?"');
#$range = str_split('qwertyuiopasdfghkjklzxcvbnm1234567890!@#$%^*&()_+-=[]\{}|;\':",./<>?');
$range = [0, 1, '[', ']', '$', '%'];
#$range = ['ア','チ','ナ','フ'];
$event = new \prggmr\Event();
// Width of all chars
$event->line_width = 130;
// Number of rain elements at a given time
$event->rain = round($event->line_width * .75);
// Interval reference for rain
$event->rain_elements = new stdClass();
// current rain element count
$event->rain_count = 0;
interval(function() use ($range){
    for ($i=0;$i!=$this->line_width;$i++) {
        // pick an element
        $element =  $range[array_rand($range)];
        // Calculate rain
        if ($this->rain_count <= $this->rain) {
            // add a new rain element
            do {
                $col = rand(1, $this->line_width - 1);
            } while (isset($this->rain_elements->{$col}));
            $this->rain_elements->{$col} = (rand(0, 10) >= 10) ? [true, rand(15, 25)] : [false, rand(15, 25)];
            $this->rain_count++;
        }
        if (isset($this->rain_elements->$i)) {
            $this->rain_elements->{$i}[1]--;
            if ($this->rain_elements->{$i}[0] === false) {
                echo " ";
            } else {
                echo "\033[1;37m".$element."\033[0m";
                //echo $element;
            }
            if ($this->rain_elements->{$i}[1] <= 0) {
                $this->rain_count--;
                unset($this->rain_elements->$i);
            }
        } else {
            if (rand(0, 10) >= 9) {
                echo "\033[1;32m".$element."\033[0m";
            } else {
                echo $element;
            }
        }
    }
    echo PHP_EOL;
}, 40)[0]->event($event);
