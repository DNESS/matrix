<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '2.0.0');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');


// DISABLE HISTORY
xp_set_signal_history(false);

xp_import('time');

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
// $symbols = str_split(mb_detect_encoding('夏でも底に冷たさをもつ青いそら', "UTF-8,ISO-8859-1"));
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
        // default:
        //     exit(sprintf(
        //         "Unknown option '%s'\n%s",
        //         $_i,
        //         $usage
        //     ));
        //     break;
    }
};

if (XPSPL_DEBUG) {
    $fps = true;
}

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
    time\awake($speed, function() use ($color_codes){
        global $color_use;
        $color_use = $color_codes[array_rand($color_codes)];
    }, TIME_MILLISECONDS);
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
// $screen = fopen(STDOUT, 'w+');

/**
 * Performs a time awake signal process for creating the matrix,
 *
 * @signal  time\awake
 */
time\awake($speed, function($matrix) use (
        $fps, $modulus, $color_use, $speed
    ){
    if (!isset($matrix->matrix)) {
        // rows
        $matrix->columns = exec('tput cols');
        $matrix->rows = exec('tput lines');
        // Count
        $matrix->iteration = 0;
        // the current matrix
        $matrix->matrix = [];
        // movement
        $matrix->mtx = [];
        // spaces
        $matrix->lines = [];
        // white head
        $matrix->cols = [];
        // welcome message
        $matrix->message = str_split(MESSAGE);
        $matrix->msg_out = '';
        // average run speed
        $matrix->average = [];
        // current average run speed
        $matrix->current = 0.0000000000000000000000000000;
    }
    for ($i=0;$i<=$matrix->columns;$i++) {
        if (isset($matrix->mtx[$i][0]) && $matrix->mtx[$i][0] <= 0) {
            $matrix->mtx[$i] = [rand($matrix->rows, $matrix->rows * 2), (rand(0, 10)>=4)];
        }
        if (isset($matrix->lines[$i][0]) && $matrix->lines[$i][0] <= 0) {
            $matrix->lines[$i] = [rand(10, 15), rand(0, 10) >= 6, true];
        }
    }
    $start = milliseconds();
    for ($y = $matrix->rows; $y >= 0 ; $y--) {
        for ($x = 0; $x <= $matrix->columns - 1; $x++) {
            if (isset($matrix->mtx[$x][0])){
                --$matrix->mtx[$x][0];
            } else {
                $matrix->mtx[$x][0] = $matrix->columns;
            }
            if (!isset($matrix->matrix[$y][$x]) || $y == 0) {
                if (isset($matrix->lines[$x][0])) {
                    --$matrix->lines[$x][0];
                } else {
                    $matrix->lines[$x][0] = $matrix->rows;
                }
                $char = (isset($matrix->lines[$x][1]) && $matrix->lines[$x][1]) ? get_char(false) : get_char(true);
                $matrix->matrix[$y][$x] = $char;
            } elseif (isset($matrix->mtx[$x][1]) && $matrix->mtx[$x][1]) {
                $newchar = $matrix->matrix[$y - 1][$x];
                if ($newchar != " " && isset($matrix->cols[$x]) && $matrix->cols[$x] === true) {
                    $matrix->cols[$x] = $y;
                }
                if (isset($matrix->cols[$x]) && $matrix->cols[$x] == $y) {
                    $color = '37';
                    ++$matrix->cols[$x];
                    if ($newchar != " ") {
                        $newchar = get_char(false);
                    }
                } else {
                    $force = false;
                    $color = $color_use;
                }
                if ($x % $modulus) {
                    $matrix->matrix[$y][$x] = " ";
                } else {
                    $matrix->matrix[$y][$x] = $newchar;
                    if ($matrix->matrix[$y][$x] != " ") {
                        if(rand(0, 10)>=10 && $matrix->cols[$x] != $y) {
                            $random = get_char(false);
                            $matrix->matrix[$y][$x] = get_char(false);
                        }
                    } else {
                        $matrix->cols[$x] = true;
                    }
                }
            }
        }
    }
    $end = milliseconds();
    // Load the matrix
    // if ($matrix->iteration >= ($matrix->rows + count($matrix->message))) {
        $output = "";
        for ($y = 0; $y <= $matrix->rows - 1; $y++) {
            if ($fps && $y == $matrix->rows - 1) {
                $matrix->average[] = $start - $end;
                if (count($matrix->average) >= rand(10, 50)) {
                    $average = $end - $start;
                    $matrix->current = $speed - ($end - $start);
                    if ($matrix->current < 1) {
                        if ($matrix->current > 0) {
                            $matrix->current = 'Buffer Left (us) : ' . ($matrix->current * 100);
                        } else {
                            $matrix->current = 'Overflow (ms) : ' . $matrix->current;
                        }
                    } else {
                        $matrix->current = 'Buffer Left (ms) : ' . $matrix->current;
                    }
                    $matrix->current = $matrix->current . PHP_EOL . 'Next Proces Time : '. xpspl()
                    ->get_routine()
                    ->get_idle()->get_idle()
                    ->get_time_until() . ' (ms)';
                    $matrix->current = $matrix->current . PHP_EOL . 'AVG Process Time : '. $average . ' (ms)';
                    $matrix->current = $matrix->current . PHP_EOL . 'Size : ' . $matrix->columns . 'x' . $matrix->rows;
                    $matrix->current = $matrix->current . PHP_EOL . 'Event : ' . spl_object_hash($matrix);
                    $matrix->current = $matrix->current . PHP_EOL . 'History : ' . count(xp_signal_history());
                    $matrix->average = [];
                }
                $output .= PHP_EOL . $matrix->current;
            } else {
                $xlength = count($matrix->matrix[$y]);
                for ($x = 0;$x != $xlength; $x++ ){
                    if (null == $matrix->matrix[$y][$x]) {
                        $output .= " ";
                    } else {
                        $output .= utf8_encode($matrix->matrix[$y][$x]);
                    }
                }
            }
        }
    // } else {
    //     if ($matrix->iteration <= count($matrix->message)) {
    //         $matrix->msg_out .= get_color($matrix->message[$matrix->iteration]);
    //     } else {
    //         $matrix->msg_out .= get_color(".");
    //     }
    //     $float = (($matrix->iteration + 1) / (count($matrix->message) + ($matrix->rows)));
    //     $percentage = round($float * 100, 0);
    //     $output = $matrix->msg_out . PHP_EOL . get_color("$percentage% [");
    //     $bar_width = $matrix->columns - 10;
    //     $bar_count = round($bar_width * $float, 0);
    //     $output .= str_repeat(get_color("="), $bar_count);
    //     $output .= str_repeat(" ", $bar_width - $bar_count);
    //     $output .= get_color("]");
    //     for ($y = 0; $y <= $matrix->rows - 4; $y++) {
    //         $output .= str_repeat(" ", $matrix->columns);
    //         $output .= PHP_EOL;
    //     }
    // }
    $matrix->last_render_time = $start;
    echo $output;
    $matrix->iteration++;
}, TIME_MILLISECONDS);
