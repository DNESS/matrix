<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '2.0.0');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');


// DISABLE HISTORY
set_signal_history(false);

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
    $speed = 0;
}

function get_color($char, $color = null) {
    global $color_use;
    if (null === $color) {
        $color = $color_use;
    }
    if (mt_rand(0, 10)>=9 || $color == '37') {
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
        $color_use = $color_codes[array_mt_rand($color_codes)];
    }), TIME_MILLISECONDS);
}

/**
 * Returns 
 * @param  boolean $space [description]
 * @return [type]         [description]
 */
function get_char($space = null, $char = null) {
    global $symbols;
    if (null !== $char) {
        return $char;
    }
    // echo PHP_INT_MAX >> 56;
    // var_dump(!(mt_rand(0, PHP_INT_MAX) << 30));// >> (PHP_INT_MAX >> 65));
    if ($space || null === $space && mt_rand(0, 10) >= 6) {
        return " ";
    }
    return $symbols[array_rand($symbols)];
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

function fill_draw_values()
{
    $result = [];
    $args = func_get_args();
    foreach ($args as $_v) {
        foreach ($_v as $_value) {
            $result[$_value] = true;
        }
    }
    return $result;
}

/**
 * Returns the last bit drawn that is not a space on the given x,y.
 *
 * If none is encountered a random get_char is returned.
 */
function get_last_draw_point($matrix, $x, $y)
{
    for ($y;$y>-1;$y--) {
        if (isset($matrix->matrix[$y][$x]) && !isset($matrix->draw[$y][$x])) {
            if (mt_rand(0, 100) >= 50) {
                return get_char();
            }
            return $matrix->matrix[$y][$x];
        }
    }
}

/**
 * Performs a time awake signal process for creating the matrix,
 * 
 * @signal  time\awake
 */
time\awake($speed, null_exhaust(function($matrix) use (
        $fps, $modulus, $color_use, $speed
    ){
    if (!isset($matrix->matrix)) {
        // rows
        $matrix->columns = exec('tput cols');
        $matrix->rows = exec('tput lines');
        // the current matrix
        $matrix->matrix = [];
        // average run speed
        $matrix->average = [];
        // current average run speed
        $matrix->current = 0.0;
        $matrix->php = 'draw';
        // draw cord
        $matrix->draw = [
            // I
            10 => fill_draw_values(
                range(10, 22),
                range(30, 32)
            ),
            11 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            12 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            13 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            14 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            15 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            16 => fill_draw_values(
                range(15, 16),
                range(28, 29),
                range(34, 35)
            ),
            17 => fill_draw_values(
                range(10, 22),
                range(30, 32)
            ),
        ];
    }
    $start = milliseconds();
    for ($y = $matrix->rows; $y >= 0 ; $y--) {
        for ($x = 0; $x <= $matrix->columns; ++$x) {
            if ($y == 0) {
                $matrix->matrix[$y][$x] = get_char();
             } else {
                $matrix->matrix[$y][$x] = (
                    isset($matrix->draw[$y][$x])
                ) ? "#" : get_last_draw_point($matrix, $x, $y);
            }
        }
    }
    $end = milliseconds();
    // Matrix load
    // @todo Shorten the loop
    $output = "";
    for ($y = 0; $y <= $matrix->rows - 1; $y++) {
        // Debug
        if ($fps && $y == 0) {
            $matrix->average[] = $start - $end;
            if (count($matrix->average) >= mt_rand(10, 50)) {
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
                $matrix->current = $matrix->current . PHP_EOL . 'History : ' . count(signal_history());
                $matrix->average = [];
            }
            $output .= PHP_EOL . $matrix->current;
        } else {
            for ($x = 0;$x < $matrix->columns; $x++ ){
                $output .= $matrix->matrix[$y][$x];
            }
        }
        $output .= PHP_EOL;
    }
    echo $output;
}), TIME_MILLISECONDS);
