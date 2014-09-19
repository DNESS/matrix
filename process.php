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
        $modulus = 2;
        $color_use = 1;
        if (!isset($sig->mtx)) {
            $sig->mtx = [];
            $sig->lines = [];
            $sig->cols = [];
        }
        $start = milliseconds();
        // for ($y = $sig->rows; $y >= 0 ; --$y) {
       for ($i=0;$i<=$sig->columns;$i++) {
            if (isset($sig->mtx[$i][0]) && $sig->mtx[$i][0] <= 0) {
                $sig->mtx[$i] = [rand($sig->rows, $sig->rows * 2), (rand(0, 10)>=4)];
            }
            if (isset($sig->lines[$i][0]) && $sig->lines[$i][0] <= 0) {
                $sig->lines[$i] = [rand(10, 15), rand(0, 10) >= 6, true];
            }
        }
        $start = milliseconds();
        for ($y = $sig->rows; $y >= 0 ; $y--) {
            for ($x = 0; $x <= $sig->columns - 1; $x++) {
                if (isset($sig->draw[$y][$x])) {
                    $sig->matrix[$y][$x] = $sig->draw[$y][$x];
                }
                if (isset($sig->mtx[$x][0])){
                    --$sig->mtx[$x][0];
                } else {
                    $sig->mtx[$x][0] = $sig->columns;
                }
                if (!isset($sig->matrix[$y][$x]) || $y == 0) {
                    if (isset($sig->lines[$x][0])) {
                        --$sig->lines[$x][0];
                    } else {
                        $sig->lines[$x][0] = $sig->rows;
                    }
                    $char = (isset($sig->lines[$x][1]) && $sig->lines[$x][1]) ? get_char(false) : get_char(true);
                    $sig->matrix[$y][$x] = $char;
                } elseif (isset($sig->mtx[$x][1]) && $sig->mtx[$x][1]) {
                    $newchar = $sig->matrix[$y - 1][$x];
                    if ($newchar != " " && isset($sig->cols[$x]) && $sig->cols[$x] === true) {
                        $sig->cols[$x] = $y;
                    }
                    if (isset($sig->cols[$x]) && $sig->cols[$x] == $y) {
                        $color = '37';
                        ++$sig->cols[$x];
                        if ($newchar != " ") {
                            $newchar = get_char(false);
                        }
                    } else {
                        $force = false;
                        $color = $color_use;
                    }
                    if ($x % $modulus) {
                        $sig->matrix[$y][$x] = " ";
                    } else {
                        $sig->matrix[$y][$x] = $newchar;
                        if ($sig->matrix[$y][$x] != " ") {
                            if(rand(0, 10)>=10 && $sig->cols[$x] != $y) {
                                $random = get_char(false);
                                $sig->matrix[$y][$x] = get_char(false);
                            }
                        } else {
                            $sig->cols[$x] = true;
                        }
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
        // if (mt_rand(10, 50)) {
        //     $sig->current = current_signal()->get_time() - end($sig->average);
        //     // readjust the run speed
        //     /// current_signal()->modify_time(mt_rand(0, 50), current_signal()->get_instruction());
        //     $average = (count($sig->average) / array_sum($sig->average));
        //     if ($average > 0) {
        //         if ($average < 1) {
        //             $instruction = TIME_MICROSECONDS;
        //         } else {
        //             $instruction = TIME_MILLISECONDS;
        //         }
        //         if (count($sig->average) > 5) {
        //             // current_signal()->modify_time(
        //             //     $average,
        //             //     $instruction
        //             // );
        //         }
        //     }
        //     if (count($sig->average) >= 1000000) {
        //         for($i=0;$i<50000;$i++) {
        //             array_shift($sig->average);
        //         }
        //     }
        // }
        // $sub_rows = 0;
        // if (XPSPL_DEBUG) {
        //     $log = [];
        //     // $sig->current = current_signal()->get_idle()->get_time_left();
        //     if ($sig->current < 1) {
        //         if ($sig->current > 0) {
        //             $log[] = 'Buffer Left ('.current_signal()->get_instruction().') : ' . ($sig->current * 100);
        //         } else {
        //             $log[] = 'Overflow ('.current_signal()->get_instruction().') : ' . $sig->current;
        //         }
        //     } else {
        //         $log[] = 'Buffer Left ('.current_signal()->get_instruction().') : ' . $sig->current;
        //     }
        //     // $sig->current = $sig->current . PHP_EOL . 'Next Proces Time : '. xpspl()
        //     // ->get_routine()
        //     // ->get_idle()->get_idle()
        //     // ->get_time_until() . ' (ms)';
        //     array_merge($log, [
        //         'AVG Process Time : '. (array_sum($sig->average) / count($sig->average)) . ' ('.current_signal()->get_instruction().')',
        //         'Size : ' . $sig->columns . 'x' . $sig->rows,
        //         'Event : ' . spl_object_hash($sig),
        //         'History : ' . count(signal_history())
        //     ]);
        //     foreach ($log as $_log) {
        //         logger(XPSPL_LOG)->debug($_log);
        //     }
        //     $sig->chop = count($_log);
        // }
        // @todo Use ncurses!
        $c = (!XPSPL_DEBUG) ? $sig->rows - 1 : ($sig->rows - $sig->chop);
        for ($y = 0; $y <= $c; $y++) {
            for ($x = 0;$x < $sig->columns; $x++ ){
                ncurses_mvwaddstr($sig->screen, $y, $x, $sig->matrix[$y][$x]);
                // $output .= " ";
            }
        }
        ncurses_wrefresh($sig->screen);
    }
}