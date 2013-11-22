<?php
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Returns a character wrapped in the provided color code.
 * 
 * @param  [type] $char  [description]
 * @param  [type] $color [description]
 * 
 * @return [type]        [description]
 */
function get_color($char, $color = '37') {
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

/**
 * Returns a character.
 * 
 * @param  boolean $space [description]
 * @return [type]         [description]
 */
function get_char($space = null, $char = null) {
    if (null !== $char) {
        return $char;
    }
    if ($space || null === $space && mt_rand(0, 10) >= 3) {
        return MATRIX_SPACE_CHAR;
    }
    return call_user_func(function(){
        return MATRIX_CHARACTERS;
    })[mt_rand(0, 64)];
    if (mt_rand(0, 10) >= 5) {
        return (mt_rand(0, 10) >= 5) ? '0' : MATRIX_COLOR_CHAR;
    } else {
        return '1';
    }
}

/**
 * Fills draw value coordinates.
 * 
 * @return  array
 */
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
 * @experimental
 * 
 * Returns the last bit drawn that is not a space on the given x,y.
 *
 * If none is encountered a random get_char is returned.
 */
function get_last_draw_point($matrix, $x, $y) {
    if ($y === 1) {
        return $matrix->matrix[0][$x];
    }
    for ($i=$y;$i>0;--$i) {
        if (isset($matrix->matrix[$i][$x]) && !isset($matrix->draw[$i][$x])) {
            return $matrix->matrix[$i][$x];
        }
    }
}

/**
 * Merges draw coordinate letters.
 *
 * @return  array
 */
function combine_letter_coordinates()
{
    $return = [];
    $args = func_get_args();
    $last_x = 0;
    foreach ($args as $_letter) {
        if ($last_x != 0) {
            $_letter->move_left($last_x + 2);
        }
        foreach ($_letter->get_coordinates() as $_y => $_x) {
            foreach ($_x as $_index => $_true) {
                $return[$_y][$_index] = $_letter->get_character();
                if ($_index > $last_x) {
                    $last_x = $_index;
                }
            }
        }
    }
    return $return;
}

/**
 * Parses an ASCII art file into draw coordinates.
 *
 * @return  array
 */
function parse_ascii_art($ascii, $color = false)
{
    $coord = [];
    $lines = explode("\n", $ascii);
    foreach ($lines as $_y => $_line) {
        $array = str_split($_line);
        foreach ($array as $_x => $_char) {
            if ($_char == " ") {
                continue;
            }
            $coord[$_y][$_x] = (!$color) ? $_char : get_color($_char);
        }
    }
    return $coord;
}

/**
 * Creates an ascii animation from a file.
 *
 * @return  array
 */
function create_ascii_animation($filename, $interval, $matrix, $length = null, $instruction = TIME_SECONDS) {
    import('time');
    $ascii = file_get_contents($filename);
    $frames = [];
    $animations = explode('--------------------------------------------------------------------------------', $ascii);
    foreach ($animations as $_ascii) {
        $coord = [];
        $lines = explode("\n", $_ascii);
        foreach ($lines as $_y => $_line) {
            $array = str_split($_line);
            foreach ($array as $_x => $_char) {
                if ($_char != " ") {
                    $coord[$_y][$_x] = get_color($_char);
                }
            }
        }
        $frames[] = $coord;
    }
    time\awake($interval, exhaust($length, function($time) use ($matrix, $frames){
        if (!isset($time->count)) {
            $time->count = 0;
        } else {
            if ($time->count >= (count($frames) - 1)) {
                $time->count = 0;
            } else {
                ++$time->count;
            }
            $matrix->set_draw_coordinates($frames[$time->count], true);
        }
    }), $instruction);
}