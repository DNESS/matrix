<?php
namespace matrix\letters;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * N
 *
 * Draws the letter N in the matrix.
 */
class N extends Letter {

    /**
     * Coordinates for drawing the letter N.
     * 
     * @var  array
     */
    public function __construct()
    {
        parent::__construct();
        $this->set_coordinates([
            1 => fill_draw_values(
                range(1, 5),
                range(10, 11)
            ),
            2 => fill_draw_values(
                range(1, 5),
                range(10, 11)
            ),
            3 => fill_draw_values(
                range(1, 2),
                range(5, 6),
                range(10, 11)
            ),
            4 => fill_draw_values(
                range(1, 2),
                range(6, 7),
                range(10, 11)
            ),
            5 => fill_draw_values(
                range(1, 2),
                range(7, 8),
                range(10, 11)
            ),
            6 => fill_draw_values(
                range(1, 2),
                range(8, 9),
                range(10, 11)
            ),
            7 => fill_draw_values(
                range(1, 2),
                range(9, 10),
                range(10, 11)
            ),
        ]);
    }
}