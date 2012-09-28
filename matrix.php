<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '1.0.0');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');

prggmr\load_module('time');

/**
 * The fucking matrix ... this shit is awesome.
 */
$usage = "usage: pmatrix [fhvz] [:cimst]

Current options:
  -c/--color    Color [default,grey,red,green,gold,blue,purple,teal] Default: Green
  -f/--fps      Display frame rate
  -i/--speed    The matrix speed. Default = 85
  -h/--help     Show this help message.
  -m/--message  Use the given message on startup.
  -r/--modulus  Modulus to use for space between cols. Default: 2
  -s/--symbols  Symbols to use in the matrix.
  -t/--time     Length of time to run in milliseconds.
  -v/--version  Displays current matrix version.
  -z            Color shift
";

$options = getopt(
    'qwer:t:yui:opas:dfghjklzxc:vbnm:',
    array(
        'help', 'version', 'time:', 'color:', 'symbols:', 'speed:',
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
            print("prggmr matrix version ".MATRIX_VERSION.PHP_EOL."By: ".MATRIX_MASTERMIND.PHP_EOL);
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
prggmr\module\time\interval($speed, function() use ($color_codes){
    global $color_use;
    $color_use = $color_codes[array_rand($color_codes)];
});
}

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
    prggmr\module\time\timeout(function(){
        prggmr\shutdown();
    }, $ttr);
}
// Custom Event
prggmr\module\time\interval($speed, function() use ($fps, $modulus){
    global $color_use;
    $cols = exec('tput cols');
    $rows = exec('tput lines');
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
        $this->message = str_split(MESSAGE);
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
    if ($this->iteration >= ($rows + count($this->message))) {
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
        if ($this->iteration <= count($this->message)) {
            $this->msg_out .= get_color($this->message[$this->iteration]); 
        } else {
            $this->msg_out .= get_color(".");
        }
        $float = (($this->iteration + 1) / (count($this->message) + ($rows)));
        $percentage = round($float * 100, 0);
        $output = $this->msg_out . PHP_EOL . get_color("$percentage% [");
        $bar_width = $cols - 10;
        $bar_count = round($bar_width * $float, 0);
        $output .= str_repeat(get_color("="), $bar_count);
        $output .= str_repeat(" ", $bar_width - $bar_count);
        $output .= get_color("]");
        for ($y = 0; $y <= $rows - 4; $y++) {
            $output .= str_repeat(" ", $cols);
            $output .= PHP_EOL;
        }
    }
    $this->last_render_time = $start;
    echo $output;
    $this->iteration++;
});