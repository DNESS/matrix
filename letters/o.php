<?php
namespace matrix\letters;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * O
 *
 * Draws the letter O in the matrix.
 */
class O extends Letter {

    /**
     * Coordinates for drawing the letter O.
     * 
     * @var  array
     */
    public function __construct()
    {
        parent::__construct();
        $this->set_coordinates([
            1 => fill_draw_values(
                range(2, 5)
            ),
            2 => fill_draw_values(
                range(1, 2),
                range(5, 6)
            ),
            3 => fill_draw_values(
                range(1, 2),
                range(5, 6)
            ),
            4 => fill_draw_values(
                range(1, 2),
                range(5, 6)
            ),
            5 => fill_draw_values(
                range(1, 2),
                range(5, 6)
            ),
            6 => fill_draw_values(
                range(1, 2),
                range(5, 6)
            ),
            7 => fill_draw_values(
                range(2, 5)
            )
        ]);
    }
}