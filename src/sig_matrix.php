<?php
namespace matrix;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

xp_import('time');

use time\SIG_Awake;

/**
 * Matrix
 *
 * Runs the matrix process.
 *
 * The matrix is for perfomance testing.
 */
class SIG_Matrix extends SIG_Awake {

    /**
     * Number of columns
     */
    public $columns = null;

    /**
     * Number of rows
     */
    public $rows = null;

    /**
     * The matrix.
     */
    public $matrix = [];

    /**
     * The average run speed.
     */
    public $average = [];

    /**
     * The current run speed.
     */
    public $current = 0.0;

    /**
     * Draw coordinates
     */
    public $draw = [];

    /**
     * Constructor
     *
     * Gets the number of columns and rows to draw and sets the SIG_Awake's
     * time and instruction.
     *
     * @param  integer  $time  Time to sleep.
     * @param  integer  $instruction  Time to instruction to sleep by.
     * @default  TIME_MICROSECONDS
     */
    public function __construct($time, $instruction = TIME_MILLISECONDS)
    {
        parent::__construct($time, $instruction);
        // columns
        $this->columns = exec('tput cols');
        // rows
        $this->rows = exec('tput lines');
    }

    /**
     * Sets the draw coordinates.
     *
     * @param  array  $coordinates  Coordinate x,y  draw values
     * @param  boolean  $center  Center in the middle of the screen
     *
     * @return  void
     */
    public function set_draw_coordinates($coordinates, $center = false)
    {
        if ($center) {
            $max_x = 0;
            foreach ($coordinates as $_y => $_xs) {
                if (max(array_keys($_xs)) > $max_x) {
                    $max_x = max(array_keys($_xs));
                }
            }
            $move_left = floor(($this->columns - $max_x) / 2);
            foreach ($coordinates as $_y => $_xs) {
                $new_x = [];
                foreach ($_xs as $_x => $_char) {
                    $new_x[$_x + $move_left] = $_char;
                }
                $coordinates[$_y] = $new_x;
            }
            $y = floor(($this->rows - end(array_keys($coordinates))) / 2);
            $new_coordinates = [];
            foreach ($coordinates as $_y => $_xs) {
                $new_coordinates[$_y + $y] = $_xs;
            }
            $coordinates = $new_coordinates;
        }
        $this->draw = $coordinates;
        return $this;
    }
}