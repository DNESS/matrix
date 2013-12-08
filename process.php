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
                $sig->matrix[$y][$x] = MATRIX_SPACE_CHAR;
                $x = $x + 1;
                if ($y == 0) {
                    $sig->matrix[$y][$x] = get_char();
                 } else {
                    // if (isset($sig->draw[$y][$x])) {
                    //     $sig->matrix[$y][$x] = $sig->draw[$y][$x];    
                    // } else {
                        // if (mt_rand(0, 10) > 1) {
                            if (isset($sig->draw[$y][$x])){// ||
                                // $sig->matrix[($y - 1)][$x] == MATRIX_FADE_CHAR) {
                                // if (mt_rand(0, 10) > 2) {
                                //     $sig->matrix[$y][$x] = MATRIX_FADE_CHAR;
                                // } else {
                                    $sig->matrix[$y][$x] = $sig->draw[$y][$x];
                                // }
                            } else {
                                // if (mt_rand(0, 10) > 2) {
                                    // if (mt_rand(0, 10) > 8) {
                                        // $sig->matrix[$y][$x] = get_char();
                                    // } else {
                                    // if (isset($sig->matrix[($y-1)][$x])) { 
                                    if (mt_rand(0, 10) > 8) {
                                        if (isset($sig->matrix[($y-1)][$x])) {
                                            $sig->matrix[$y][$x] = $sig->matrix[($y - 1)][$x];
                                        }
                                    } else {
                                        if (mt_rand(0, 10) == 10) {
                                            // $sig->matrix[$y][$x] = get_char();
                                        }
                                    }
                                    // } else {
                                        // $sig->matrix[$y][$x] = MATRIX_SPACE_CHAR;
                                    // }
                                    // }
                                // }
                            }
                        // }
                    // }
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
        $sig->average[] = $end - $start;
        // Randomly take samples for the averge run speed and modify time
        if (mt_rand(10, 50)) {
            $sig->current = current_signal()->get_time() - end($sig->average);
            // readjust the run speed
            /// current_signal()->modify_time(mt_rand(0, 50), current_signal()->get_instruction());
            $average = (count($sig->average) / array_sum($sig->average));
            if ($average > 0) {
                if ($average < 1) {
                    $instruction = TIME_MICROSECONDS;
                } else {
                    $instruction = TIME_MILLISECONDS;
                }
                if (count($sig->average) > 5) {
                    // current_signal()->modify_time(
                    //     $average,
                    //     $instruction
                    // );
                }
            }
            if (count($sig->average) >= 1000000) {
                for($i=0;$i<50000;$i++) {
                    array_shift($sig->average);
                }
            }
        }
        $sub_rows = 0;
        if (XPSPL_DEBUG) {
            $log = [];
            // $sig->current = current_signal()->get_idle()->get_time_left();
            if ($sig->current < 1) {
                if ($sig->current > 0) {
                    $log[] = 'Buffer Left ('.current_signal()->get_instruction().') : ' . ($sig->current * 100);
                } else {
                    $log[] = 'Overflow ('.current_signal()->get_instruction().') : ' . $sig->current;
                }
            } else {
                $log[] = 'Buffer Left ('.current_signal()->get_instruction().') : ' . $sig->current;
            }
            // $sig->current = $sig->current . PHP_EOL . 'Next Proces Time : '. xpspl()
            // ->get_routine()
            // ->get_idle()->get_idle()
            // ->get_time_until() . ' (ms)';
            array_merge($log, [
                'AVG Process Time : '. (array_sum($sig->average) / count($sig->average)) . ' ('.current_signal()->get_instruction().')',
                'Size : ' . $sig->columns . 'x' . $sig->rows,
                'Event : ' . spl_object_hash($sig),
                'History : ' . count(signal_history())
            ]);
            foreach ($log as $_log) {
                logger(XPSPL_LOG)->debug($_log);
            }
            $sig->chop = count($_log);
        }
        // @todo Use ncurses!
        $c = (!XPSPL_DEBUG) ? $sig->rows - 1 : ($sig->rows - $sig->chop);
        for ($y = 0; $y <= $c; $y++) {
            for ($x = 0;$x < $sig->columns; $x++ ){
                $output .= $sig->matrix[$y][$x];
                // $output .= " ";
            }
            $output .= PHP_EOL;
        }
        echo $output;
    }
}