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
$speed = 0;
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

/**
 * Returns
 * @param  boolean $space [description]
 * @return [type]         [description]
 */
function get_char($space = true) {
    // Characters
    $range = array_merge(str_split('~!@#$%^&*()_+-=[]\{}|<>?,./;\':'), range('A', 'Z'));
    // if ($space) return " ";
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


// // Init
ncurses_init();
ncurses_curs_set(0);
$screen = ncurses_newwin(0, 0, 0, 0);

/**
 * Performs a time awake signal process for creating the matrix,
 *
 * @signal  time\awake
 */
$threads = 12;
$columns = exec('tput cols');
$rows = exec('tput lines');

$columns_per_thread = floor($columns / 12);
$overage = $columns / $columns_per_thread;
if ($overage > $threads) {
    $additional = round($overage - $threads);
    if ($additional == 0) {
        $additional = 1;
    }
} else {
    $additional = 0;
}
function matrix($signal, $thread) {
    $matrix = $thread->matrix;
    if (!isset($matrix->matrix)) {
        // the current matrix
        $matrix->matrix = [];
        // movement
        $matrix->mtx = [];
        // spaces
        $matrix->lines = [];
        // white head
        $matrix->cols = [];
    }
    for ($i=0;$i<=$thread->columns;$i++) {
        if (isset($matrix->mtx[$i][0]) && $matrix->mtx[$i][0] <= 0) {
            $matrix->mtx[$i] = [rand($thread->rows, $thread->rows * 2), (rand(0, 10)>=4)];
        }
        if (isset($matrix->lines[$i][0]) && $matrix->lines[$i][0] <= 0) {
            $matrix->lines[$i] = [rand(10, 15), rand(0, 10) >= 6, true];
        }
    }
    for ($y = $thread->rows; $y >= 0 ; $y--) {
        for ($x = 0; $x <= $thread->columns - 1; $x++) {
            if (isset($matrix->mtx[$x][0])){
                --$matrix->mtx[$x][0];
            } else {
                $matrix->mtx[$x][0] = $thread->columns;
            }
            if (!isset($matrix->matrix[$y][$x]) || $y == 0) {
                if (isset($matrix->lines[$x][0])) {
                    --$matrix->lines[$x][0];
                } else {
                    $matrix->lines[$x][0] = $thread->rows;
                }
                $char = (isset($matrix->lines[$x][1]) && $matrix->lines[$x][1]) ? get_char(false) : get_char(true);
                // var_dump($char);
                $matrix->matrix[$y][$x] = $char;
            } elseif (isset($matrix->mtx[$x][1]) && $matrix->mtx[$x][1]) {
                $newchar = $matrix->matrix[$y - 1][$x];
                if ($newchar != " " && isset($matrix->cols[$x]) && $matrix->cols[$x] === true) {
                    $matrix->cols[$x] = $y;
                }
                if (isset($matrix->cols[$x]) && $matrix->cols[$x] == $y) {
                    ++$matrix->cols[$x];
                    if ($newchar != " ") {
                        $newchar = get_char(false);
                    }
                }
                if ($x % $matrix->modulus) {
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
    // Load the matrix
    // if ($matrix->iteration >= ($thread->rows + count($matrix->message))) {
    // Mutex::lock($thread->mutex);
    // var_dump($matrix);
    for ($y = 0; $y <= $thread->rows - 1; $y++) {
        $xlength = count($matrix->matrix[$y]);
        for ($x = 0;$x != $xlength; $x++ ){
            // echo $x . " " . $y . PHP_EOL;
            // print $matrix->matrix[$y][$x];
            // print $x+($thread->thread_id * $thread->columns_per_thread).PHP_EOL;
            // if (null == $matrix->matrix[$y][$x]) {
            //     ncurses_mvwaddstr($thread->screen, $y, $x, "A");
            // } else {
                ncurses_mvwaddstr($thread->screen, $y, $x, "A");
            // }
        }
    }
    ncurses_wrefresh($screen);
    // print "END";
    // Mutex::unlock($thread->mutex);

}

// $mutex = Mutex::create();
for ($i=0;$i<=$threads;$i++) {
    $mtx_object = new stdClass();
    $thread_id = $i;
    if ($i == $threads) {
        $columns_per_thread += $additional;
    }
    // create the threads
    $matrix = new time\SIG_Awake($speed, TIME_MILLISECONDS);
    $process = xp_threaded_process('matrix');
    $process->thread_vars = [
        'screen' => $screen,
        'modulus' => $modulus,
        'columns_per_thread' => $columns_per_thread,
        'thread_id' => $i,
        'columns' => $columns_per_thread,
        'rows' => $rows,
        // 'mutex' => $mutex,
        'matrix' => $mtx_object
    ];
    xp_signal($matrix, $process);
}
// time\awake(0, function() use ($screen){
//     ncurses_wrefresh($screen);
// });
