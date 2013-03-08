<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

define('MATRIX_VERSION', '2.0.0');
define('MATRIX_MASTERMIND', 'Nickolas Whiting');

if (!defined('MATRIX_PATH')) {
    define('MATRIX_PATH', dirname(realpath(__FILE__)));
}
set_include_path(
    MATRIX_PATH . '/..' . PATH_SEPARATOR . 
    get_include_path()
);

require_once dirname(realpath(__FILE__)).'/src/utils.php';

set_signal_history(false);

define('MATRIX_DRAW_CHAR', get_color('H'));
define('MATRIX_COLOR_CHAR', '0');
define('MATRIX_FADE_CHAR', '*');
define('MATRIX_SPACE_CHAR', " ");

if (XPSPL_DEBUG) {
    $speed = 0;
} else {
    $speed = 100;
}


$matrix = new matrix\SIG_Matrix($speed);

/**
 * Register the matrix into the processor.
 * 
 * @signal  time\awake
 */
signal(
    $matrix, 
    new matrix\Process()
);

$down = 15;
$left = 45;

// $matrix->set_draw_coordinates(parse_ascii_art(
//     'drawfiles/1.txt'
// ), true);

// $matrix->set_draw_coordinates(combine_letter_coordinates(
//     (new matrix\letters\I())->move_down($down)->move_left($left),
//     (new matrix\letters\O())->move_down($down),
//     (new matrix\letters\A())->move_down($down),
//     (new matrix\letters\N())->move_down($down),
//     (new matrix\letters\A())->move_down($down)
// ));


time\awake(3, null_exhaust(function($time) use ($matrix){
    if (!isset($time->count)) {
        $time->count = 0;
    } else {
        if ($time->count >= 5) {
            $matrix->set_draw_coordinates(parse_ascii_art(
                dirname(realpath(__FILE__)).'/drawfiles/'.mt_rand(1, 8).'.txt'
            ), true);
        } else {
            ++$time->count;
            if ($time->count < 4) {
                $matrix->set_draw_coordinates(parse_ascii_art(
                    dirname(realpath(__FILE__)).'/drawfiles/startup/'.$time->count.'.txt'
                ), true);
            }
        }
    }
}));

/**
 * Adds a count to the matrix changing the draw coords at random.
 */
// signal(
//     $matrix,
//     null_exhaust(function(matrix\SIG_Matrix $matrix){
//         if (!isset($matrix->count)) {
//             $matrix->count = 0;
//         }
//         ++$matrix->count;
//         if ($matrix->count >= 2) {
//             if (mt_rand(0, 10) >= 9) {
//                 $cords = [];
//                 for ($i = 0; $i < mt_rand(10, $matrix->rows); ++$i) {
//                     $cords[mt_rand(0, $matrix->rows)] = fill_draw_values(
//                         range(mt_rand(0, $matrix->columns), mt_rand(0, $matrix->columns))
//                     );
//                 }
//                 $matrix->set_draw_coordinates($cords);
//             } elseif (mt_rand(0, 10) >= 3) {
//                 $matrix->set_draw_coordinates(combine_letter_coordinates(
//                     new matrix\letters\I(),
//                     new matrix\letters\O(),
//                     new matrix\letters\A(),
//                     new matrix\letters\N(),
//                     new matrix\letters\A()
//                 ));
//             } else {
//                 $matrix->set_draw_coordinates([]);
//             }
//             $matrix->count = 0;
//         }
//     })
// );
