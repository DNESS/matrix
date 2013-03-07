<?php
namespace matrix;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Draw Coordinate
 *
 * Coordinates for drawing characters in the matrix.
 */
class Draw_Coordinate {

    /**
     * The draw coordinates.
     *
     * @var  array
     */
    protected $_coordinates = null;

    /**
     * The character to draw.
     *
     * @var  string
     */
    protected $_character = null;

    /**
     * Construct
     * 
     * You must declare the coordinates.
     * 
     * @var  array
     */
    public function __construct($character = MATRIX_DRAW_CHAR)
    {
        $this->_character = $character;
    }

    /**
     * Sets the draw coordinates.
     *
     * @param  array  $coordinates  Coordinates to draw.
     *
     * @return  object  \matrix\letters\Letter
     */
    public function set_coordinates($coordinates)
    {
        $this->_coordinates = $coordinates;
    }

    /**
     * Gets the draw coordinates.
     *
     * @return  aray
     */
    public function get_coordinates(/* ... */)
    {
        return $this->_coordinates;
    }

    /**
     * Moves each coordinate to the left by x spaces.
     *
     * @param  integer  $length  Length of coordinate space to move.
     *
     * @return  object  \matrix\letters\Letter
     */
    public function move_left($length)
    {
        foreach ($this->_coordinates as $_y => $_x) {
            foreach ($_x as $__x => $_v) {
                $this->_coordinates[$_y][$__x + $length] = true;
                unset($this->_coordinates[$_y][$__x]);
            }
        }
        return $this;
    }

    /**
     * Move each coordinate down x spaces.
     *
     * @param  integer  $spaces  Number of spaces to move down.
     *
     * @return  object  \matrix\letters\Letter
     */
    public function move_down($length)
    {
        foreach ($this->_coordinates as $_y => $_x) {
            foreach ($_x as $__x => $_v) {
                $this->_coordinates[$_y + $length][$__x] = true;
                unset($this->_coordinates[$_y][$__x]);
            }
        }
        return $this;
    }

    /**
     * Returns the character to draw.
     *
     * @return  string
     */
    public function get_character(/* ... */)
    {
        return $this->_character;
    }
}