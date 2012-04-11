<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '0.1.1');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');

/**
 * The fucking matrix ... this shit is awesome.
 */
$usage = "usage: prggmr [options...] matrix.php

Current options:
  -c/--color    Color [red,green,gold,blue,purple,teal]
  -f/--fps      Display frame rate
  -h/--help     Show this help message.
  -s/--symbols  Symbols to use in the matrix
  -t/--time     Length of time to run in milliseconds.
  -v/--version  Displays current matrix version.
  -z            Color shift
";

$options = getopt(
    'qwert:yuiopasdfghjklzxc:vbnm',
    array(
        'help', 'version', 'time:', 'color:', 'symbols:'
    )
);

$color_codes = [
    'red' => '31',
    'green' => '32',
    'gold' => '33',
    'blue' => '34',
    'purple' => '35',
    'teal' => '36'
];

$tmp = $argv;
$fps = false;
$ttr = null;
$color_use = '32';
$shift = false;
$symbols = array_merge(str_split('~!@#$%^&*()_+-=[]\{}|<>?,./;\':'), range('A', 'Z'));
// parse args and check for options
foreach ($options as $_i => $_arg) {
    switch ($_i) {
        case 'c':
        case 'color':
            if (false === $_arg || !isset($color_codes[$_arg])) {
                exit("invalid color\nColors Available : ".implode(array_keys($color_codes)));
            }
            $color_use = $color_codes[$_arg];
        case 'p':
        case 'passthru':
            continue;
            break;
        case 't':
        case 'time':
            if (false === $_arg || !is_int($_arg + 0)) {
                exit("invalid option 't'\n".$usage);
            }
            $ttr = $_arg + 0;
            break;
        case 's':
        case 'symbols':
            $symbols = str_split($_arg);
            if (!is_array($symbols)) exit("invalid symbols");
            break;
        case 'h':
        case 'help':
            exit($usage);
            break;
        case 'v':
        case 'version':
            exit("prggmr matrix version ".MATRIX_VERSION.PHP_EOL."By: ".MATRIX_MASTERMIND);
            break;
        case 'f':
        case 'fps':
            $fps = true;
            break;
        case 'z':
            $shift = true;
            break;
        default:
            exit(sprintf(
                "Unknown option '%s'\n%s",
                $_i,
                $usage
            ));
            break;
    }
};

function get_color($char, $color = null) {
    global $color_use;
    if (null === $color) {
        $color = $color_use;
    }
    if (rand(0, 10)>=9 || $color == '37') {
        $bold = '1;';
    } else {
        $bold = '';
    }
    return "\033[".$bold.$color."m".$char."\033[0m";
}

if ($shift) {
interval(function($color_use, $color_codes){
    global $color_use;
    $color_use = $color_codes[array_rand($color_codes)];
}, 85, [$color_use, $color_codes]);
}

function get_char($space = true) {
    global $symbols;
    // Characters
    $range = $symbols;
    if ($space) return " ";
    $char = $range[array_rand($range)];
    return $char;
}

if (null !== $ttr) {
    timeout(function(){
        prggmr_shutdown();
    }, $ttr);
}
// Columns
$cols = exec('tput cols');
// Rows
$rows = exec('tput lines');
// Custom Event
interval(function($rows, $cols, $fps){
    global $color_use;
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
        $this->message = str_split('Loading the matrix');
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
    $start = milliseconds();
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
                    if ($newchar != " ") {
                        $newchar = get_char(false);
                    }
                } else {
                    $force = false;
                    $color = $color_use;
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
    if ($this->iteration >= count($this->message) + 3) {
        $output = "";
        for ($y = 0; $y <= $rows - 1; $y++) {
            if ($fps && $y == $rows - 1) { 
                $frate = round((1 / (milliseconds() - $start)) * 1000, 4);
                $output .= PHP_EOL . get_color(" FPS : $frate", "37", true);
            } else {
                $xlength = count($this->matrix[$y]);
                for ($x = 0;$x != $xlength; $x++ ){
                    $output .= $this->matrix[$y][$x][1];
                }
            }
            $output .= PHP_EOL;
        }
    } else {
        if ($this->iteration >= count($this->message)) {
            $this->msg_out .= get_color('.');
        } else {
            $this->msg_out .= get_color($this->message[$this->iteration]); 
        }
        $output = $this->msg_out;
        for ($y = 0; $y <= $rows - 2; $y++) {
            $output .= str_repeat(" ", ($y == 0) ? $cols - strlen($this->msg_out) : $cols);
            $output .= PHP_EOL;
        }
    }
    $this->last_render_time = $start;
    echo $output;
    $this->iteration++;
}, 85, [$rows, $cols, $fps]);