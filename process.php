<?php
namespace matrix;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Matrix
 *
 * Runs the matrix process.
 *
 * The matrix is for perfomance testing.
 */
class Process extends \XPSPL\Process {

    /**
     * Constructor
     *
     * Sets the matrix exhaust to null.
     *
     * The constructor allows for no arguments.
     */
    public function __construct()
    {
        parent::__construct();

        $this->set_exhaust(null);
    }

    /**
     * Execute the matrix.
     */
    public function execute(SIG_Matrix $sig)
    {
        $start = milliseconds();
        for ($y = $sig->rows; $y >= 0 ; --$y) {
        // for ($y = 0; $y < $sig->rows ; ++$y) {
            for ($x = 0; $x <= $sig->columns; ++$x) {
                if ($y == 0) {
                    $sig->matrix[$y][$x] = get_char();
                 } else {
                    if (isset($sig->draw[$y][$x])) {
                        $sig->matrix[$y][$x] = $sig->draw[$y][$x];    
                    } else {
                        if (mt_rand(0, 10) > 1) {
                            if (isset($sig->draw[($y - 1)][$x]) ||
                                $sig->matrix[($y - 1)][$x] == MATRIX_FADE_CHAR) {
                                if (mt_rand(0, 10) > 2) {
                                    $sig->matrix[$y][$x] = MATRIX_FADE_CHAR;
                                } else {
                                    $sig->matrix[$y][$x] = get_char();
                                }
                            } else {
                                $sig->matrix[$y][$x] = $sig->matrix[($y - 1)][$x];
                            }
                        }
                    }
                    if (!isset($sig->matrix[$y][$x])) {
                        $sig->matrix[$y][$x] = MATRIX_SPACE_CHAR;
                    }
                }
            }
        }
        $end = milliseconds();
        // Matrix load
        // @todo Shorten the debug loop!!
        $output = "";
        if (XPSPL_DEBUG) {
            $sig->average[] = $start - $end;
            if (count($sig->average) >= mt_rand(10, 50)) {
                $average = $end - $start;
                $sig->current = $speed - ($end - $start);
                if ($sig->current < 1) {
                    if ($sig->current > 0) {
                        $sig->current = 'Buffer Left (us) : ' . ($sig->current * 100);
                    } else {
                        $sig->current = 'Overflow (ms) : ' . $sig->current;
                    }
                } else {
                    $sig->current = 'Buffer Left (ms) : ' . $sig->current;
                }
                // $sig->current = $sig->current . PHP_EOL . 'Next Proces Time : '. xpspl()
                // ->get_routine()
                // ->get_idle()->get_idle()
                // ->get_time_until() . ' (ms)';
                $sig->current = $sig->current . PHP_EOL . 'AVG Process Time : '. $average . ' (ms)';
                $sig->current = $sig->current . PHP_EOL . 'Size : ' . $sig->columns . 'x' . $sig->rows;
                $sig->current = $sig->current . PHP_EOL . 'Event : ' . spl_object_hash($sig);
                $sig->current = $sig->current . PHP_EOL . 'History : ' . count(signal_history());
                $sig->average = [];
            }
            $output .= PHP_EOL . $sig->current;
        }
        // @todo Use ncurses!
        $c = (!XPSPL_DEBUG) ? $sig->rows - 1 : ($sig->rows - 10);
        for ($y = 0; $y <= $c; $y++) {
            for ($x = 0;$x < $sig->columns; $x++ ){
                $output .= $sig->matrix[$y][$x];
            }
            $output .= PHP_EOL;
        }
        echo $output;
    }
}