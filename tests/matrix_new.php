<?php

function get_color($char, $color = '32') {
    if (rand(0, 10)>=9 || $color == '37') {
        return "\033[1;".$color."m".$char."\033[0m";
    }
    return $char;
}

function get_char($space = true) {
    // Characters
    $range = array_merge(['[', ']', '$', '%'], range('a', 'z'));
    if ($space) return " ";
    $char = $range[array_rand($range)];
    return $char;
}

// Columns
$cols = exec('tput cols');
// Rows
$rows = exec('tput lines');
// Custom Event
interval(function($rows, $cols, $range){
    if (!isset($this->matrix)) {
        // Count
        $this->iteration = 0;
        // the current matrix
        $this->matrix = [];
        // movement
        $this->mtx = [];
        // spaces
        $this->lines = [];
        // white head
        $this->cols = [];
        // welcome message
        $this->message = str_split('Welcome to prggmr library matrix ... enjoy');
        $this->msg_out = '';
    }
    for ($i=0;$i<=$cols;$i++) {
        if ($this->mtx[$i][0] <= 0) {
            $this->mtx[$i] = [rand($rows, $rows * 2), (rand(0, 10)>=4)];
        }
        if ($this->lines[$i][0] <= 0) {
            $this->lines[$i] = [rand(10, 15), rand(0, 10) >= 6, true];
        }
    }
    for ($y = $rows; $y >= 0 ; $y--) {
        for ($x = 0; $x <= $cols - 1; $x++) {
            $this->mtx[$x][0]--;
            if (!isset($this->matrix[$y][$x]) || $y == 0) {
                $this->lines[$x][0]--;
                $char = ($this->lines[$x][1]) ? get_char(false) : get_char(true);
                $this->matrix[$y][$x] = [$char, $char];
            } elseif ($this->mtx[$x][1]) {
                $newchar = $this->matrix[$y - 1][$x][0];
                if ($newchar != " " && $this->cols[$x] === true) {
                    $this->cols[$x] = $y;
                }
                if ($this->cols[$x] == $y) {
                    $color = '37';
                    $this->cols[$x]++;
                } else {
                    $force = false;
                    $color = '32';
                }
                $this->matrix[$y][$x] = [$newchar, get_color($newchar, $color)];
                if ($this->matrix[$y][$x][0] != " ") {
                    if(rand(0, 10)>=10 && $this->cols[$x] != $y) {
                        $random = get_char(false);
                        $this->matrix[$y][$x] = [$random, get_color($random, $color)];
                    }
                } else {
                    $this->cols[$x] = true;
                }
            }
        }
    }
    if ($this->iteration >= count($this->message) + 5) {
        $output = "";
        for ($y = 0; $y <= $rows - 1; $y++) {
            $xlength = count($this->matrix[$y]);
            for ($x = 0;$x != $xlength; $x++ ){
                $output .= $this->matrix[$y][$x][1];
            }
            $output .= PHP_EOL;
        }
    } else {
        if ($this->iteration >= count($this->message)) {
            $this->msg_out .= '.';
        } else {
            $this->msg_out .= get_color($this->message[$this->iteration]); 
        }
        $output = $this->msg_out;
        for ($y = 0; $y <= $rows - 2; $y++) {
            $output .= str_repeat(" ", ($y == 0) ? $cols - strlen($this->msg_out) : $cols);
            $output .= PHP_EOL;
        }
    }
    echo $output;
    $this->iteration++;
}, 50, [$rows, $cols, $range]);