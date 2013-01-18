<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '2.0.0');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');

error_reporting('E_ALL');

// DISABLE HISTORY
save_signal_history(false);

import('time');

/**
 * The fucking matrix ... this shit is awesome ... nuff said.
 */
$usage = "usage: matrix [-c|--color][-f|--fps][-i|--interval]

Current options:
  -c/--color    Color [default,grey,red,green,gold,blue,purple,teal] Default: Green

  -l/--lps      Display loops per *second
  -i/--interval How fast to run. Default: 2
  -h/--help     Show this help message.
  -m/--message  Print this message on startup.
  -r/--modulus  Shift for spacing. Default: 2
  -s/--symbols  Symbols to use in the matrix.
  -v/--version  Displays current matrix version.
  -z            Color shift
";

$options = getopt(
    'qwer:t:yui:opas:dfghjklzxc:vbnm:',
    array(
        'help', 'version', 'time:', 'color:', 'symbols:', 'interval:',
        'message:', 'modulus:'
    )
);

$color_codes = [
    'default' => '1',
    'grey' => '30',
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
$color_use = '3';
$shift = false;
$speed = 85;
$symbols = array_merge(str_split('~!@#$%^&*()_+-=[]\{}|<>?,./;\':'), range('A', 'Z'));
$modulus = 2;
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
        case 'i':
        case 'speed':
            if (false === $_arg || !is_int($_arg + 0)) {
                exit("invalid option 'i'\n".$usage);
            }
            $speed = $_arg + 0;
            break;
        case 'r':
        case 'modulus':
            if (false === $_arg || !is_int($_arg + 0)) {
                exit("invalid option 'r'\n".$usage);
            }
            $modulus = $_arg + 0;
            break;
        case 'm':
        case 'message':
            if (false === $_arg || !is_string($_arg)) {
                exit("invalid option 'm'\n".$usage);
            }
            define('MESSAGE', $_arg);
            break;
        case 's':
        case 'symbols':
            if (false === $_arg || !is_string($_arg)) {
                exit("invalid option 's'\n".$usage);
            }
            $symbols = str_split($_arg);
            if (!is_array($symbols) || count($symbols) == 0) exit("invalid symbols");
            break;
        case 'h':
        case 'help':
            exit($usage);
            break;
        case 'v':
        case 'version':
            print("prggmr_matrix version ".MATRIX_VERSION.PHP_EOL."By: ".MATRIX_MASTERMIND.PHP_EOL);
            exit;
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
    // Wake every x speed and shift colors
    time\awake($speed, null_exhaust(function() use ($color_codes){
        global $color_use;
        $color_use = $color_codes[array_rand($color_codes)];
    }), TIME_MILLISECONDS);
}

/**
 * Returns 
 * @param  boolean $space [description]
 * @return [type]         [description]
 */
function get_char($space = true) {
    global $symbols;
    // Characters
    $range = $symbols;
    if ($space) return " ";
    $char = $range[array_rand($range)];
    return $char;
}

if (!defined('MESSAGE')) {
    define('MESSAGE', "Loading the matrix");
}

if (null !== $ttr) {
    time\awake(function(){
        shutdown();
    }, $ttr);
}

$screen = fopen(STDOUT);

/**
 * Performs a time awake signal process for creating the matrix,
 * 
 * @signal  time\awake
 */
time\awake($speed, null_exhaust(function() use (
        $fps, $modulus, $color_use, $speed
    ){
    if (!isset($this->matrix)) {
        // rows
        $this->columns = exec('tput cols');
        $this->rows = exec('tput lines');
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
        $this->message = str_split(MESSAGE);
        $this->msg_out = '';
        // average run speed
        $this->average = [];
        // current average run speed
        $this->current = 0.0000000000000000000000000000;
    }
    for ($i=0;$i<=$this->columns;$i++) {
        if ($this->mtx[$i][0] <= 0) {
            $this->mtx[$i] = [rand($this->rows, $this->rows * 2), (rand(0, 10)>=4)];
        }
        if ($this->lines[$i][0] <= 0) {
            $this->lines[$i] = [rand(10, 15), rand(0, 10) >= 6, true];
        }
    }
    $start = milliseconds();
    for ($y = $this->rows; $y >= 0 ; $y--) {
        for ($x = 0; $x <= $this->columns - 1; $x++) {
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
                if ($x % $modulus) {
                    $this->matrix[$y][$x] = [" ", " "];
                } else {
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
    }
    // Load the matrix
    if ($this->iteration >= ($this->rows + count($this->message))) {
        $output = "";
        for ($y = 0; $y <= $this->rows - 1; $y++) {
            if ($fps && $y == $this->rows - 1) {
                $this->average[] = xpspl()
                    ->get_routine()
                    ->get_idle()
                    ->get_time_left();
                if (count($this->average) >= rand(10, 50)) {

                    $this->current = array_sum($this->average) / count(
                        array_filter($this->average)
                    );
                    $average = $speed - $this->current;
                    if ($this->current < 1) {
                        if ($this->current > 0) {
                            $this->current = 'Buffer Left (us) : ' . ($this->current * 100);
                        } else {
                            $this->current = 'Overflow (ms) : ' . $this->current;
                        }
                    } else {
                        $this->current = 'Buffer Left (ms) : ' . $this->current;
                    }
                    $this->current = $this->current . PHP_EOL . 'AVG Process Time : '. $average . ' (ms)';
                    $this->current = $this->current . PHP_EOL . 'Size : ' . $this->columns . 'x' . $this->rows;
                    $this->average = [];
                    $this->current = $this->current . PHP_EOL . 'Event : ' . spl_object_hash($this);
                    $this->current = $this->current . PHP_EOL . 'History : ' . count(signal_history());
                }
                $output .= PHP_EOL . $this->current;
            } else {
                $xlength = count($this->matrix[$y]);
                for ($x = 0;$x != $xlength; $x++ ){
                    $output .= $this->matrix[$y][$x][1];
                }
            }
        }
    } else {
        if ($this->iteration <= count($this->message)) {
            $this->msg_out .= get_color($this->message[$this->iteration]); 
        } else {
            $this->msg_out .= get_color(".");
        }
        $float = (($this->iteration + 1) / (count($this->message) + ($this->rows)));
        $percentage = round($float * 100, 0);
        $output = $this->msg_out . PHP_EOL . get_color("$percentage% [");
        $bar_width = $this->columns - 10;
        $bar_count = round($bar_width * $float, 0);
        $output .= str_repeat(get_color("="), $bar_count);
        $output .= str_repeat(" ", $bar_width - $bar_count);
        $output .= get_color("]");
        for ($y = 0; $y <= $this->rows - 4; $y++) {
            $output .= str_repeat(" ", $this->columns);
            $output .= PHP_EOL;
        }
    }
    $this->last_render_time = $start;
    echo $output;
    $this->iteration++;
}), TIME_MILLISECONDS);
