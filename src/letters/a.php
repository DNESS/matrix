<?php
namespace matrix\letters;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * A
 *
 * Draws the letter A in the matrix.
 */
class A extends Letter {

    /**
     * Coordinates for drawing the letter A.
     * 
     * @var  array
     */
    public function __construct()
    {
        $this->set_coordinates([
            1 => fill_draw_values(
                range(3, 5)
            ),
            2 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            ),
            2 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            ),
            3 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            ),
            4 => fill_draw_values(
                range(1, 7)
            ),
            5 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            ),
            6 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            ),
            7 => fill_draw_values(
                range(1, 2),
                range(6, 7)
            )
        ]);
    }
}