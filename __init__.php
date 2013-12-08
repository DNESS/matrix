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

$file_locations = [
    '/' => ['draw_coordinate', 'process', 'sig_matrix'],
    'letters/' => ['letter','a','i','n','o'],
];

foreach ($file_locations as $path => $files) {
    foreach ($files as $file) {
        require_once dirname(realpath(__FILE__)).'/'.$path.$file.'.php';        
    }
}

require_once dirname(realpath(__FILE__)).'/utils.php';

set_signal_history(false);

define('MATRIX_DRAW_CHAR', get_color('H'));
define('MATRIX_COLOR_CHAR', '0');
define('MATRIX_FADE_CHAR', '*');
define('MATRIX_SPACE_CHAR', " ");
define('MATRIX_CHARACTERS', 'abcdefghijklmnopqrstuvwxyz!@#$%^&*()_+-=!@#$%^&*({}|:"<>?,./;\'[]\\');


$matrix = new matrix\SIG_Matrix(120);

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

import('network');

$server = network\connect('0.0.0.0', ['port' => '1337']);
$server->on_connect(null_exhaust(function(network\SIG_Connect $sig) use ($matrix){
    $std = new stdClass();
    $std->rows = $matrix->rows;
    $std->cols = $matrix->columns;
    $std->time = $matrix->get_time();
    $sig->socket->write(json_encode($std));
    $sig->socket->read();
}));
$server->on_read(null_exhaust(function(network\SIG_Read $sig_read) use ($matrix) {
    $read = trim($sig_read->socket->read());
    if (strlen($read) > 10) {
        $matrix->set_draw_coordinates(parse_ascii_art($read, false), true);
    }
}));
$server->on_disconnect(null_exhaust(function(network\SIG_Disconnect $sig) use ($matrix) {
    $socket = intval($sig_read->socket);
    if (isset($matrix->neros[$socket])) {
        unset($matrix->neros[$socket]);
    }
}));

// create_ascii_animation('drawfiles/animations/stealth.txt', 110, $matrix, null, TIME_MILLISECONDS);

// $matrix->set_draw_coordinates(parse_ascii_art(
//     'drawfiles/9.txt'
// ), true);

// $matrix->set_draw_coordinates(combine_letter_coordinates(
//     (new matrix\letters\I())->move_down($down)->move_left($left),
//     (new matrix\letters\O())->move_down($down),
//     (new matrix\letters\A())->move_down($down),
//     (new matrix\letters\N())->move_down($down),
//     (new matrix\letters\A())->move_down($down)
// ));

// time\awake(125, null_exhaust(function($time) use ($matrix){
//     if (!isset($time->frame)) {
//         $time->frame = 1;
//     } else {
//         if ($time->frame >= 4) {
//             --$time->frame;
//         } else {
//             if ($time->frame < 1) {
//                 $time->frame = 1;
//             } else {
//                 ++$time->frame;
//             }
//         }
//     }
//     $matrix->set_draw_coordinates(parse_ascii_art(
//         dirname(realpath(__FILE__)).'/drawfiles/panda/'.$time->frame.'.txt'
//     ), true);
// }), TIME_MILLISECONDS);

// time\awake(25, null_exhaust(function($time) use ($matrix){
//     if (!isset($time->count)) {
//         $time->animation = 0;
//         $time->animations = ['globe'];
//         $time->count = 0;
//     } else {
//         if ($time->count >= 5) {
//             $server = network\connect('0.0.0.0', ['port' => '1337']);
//             $server->on_connect(null_exhaust(function($sig){
//                 $sig->socket->write('START THE MATRIX STREAM');
//                 $sig->socket->read();
//             }));
//             $server->on_read(null_exhaust(function(network\SIG_Read $sig_read) use ($matrix) {
//                 $matrix->set_draw_coordinates(parse_ascii_art($sig_read->socket->read()), true);
//             }));
//             delete_signal($time);
//         } else {
//             ++$time->count;
//             if ($time->count < 100) {
//                 $matrix->set_draw_coordinates(parse_ascii_art(
//                     dirname(realpath(__FILE__)).'/drawfiles/startup/'.$time->count.'.txt'
//                 ), true);
//             }
//         }
//     }
// }), TIME_MILLISECONDS);

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
