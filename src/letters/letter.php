<?php
namespace matrix\letters;
/**
 * Copyright 2012 Nickolas Whiting. All rights reserved.
 * Use of matrix source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */

/**
 * Letter
 *
 * Draw a letter in the matrix.
 */
class Letter {

    /**
     * The draw coordinates for the letter.
     *
     * @var  array
     */
    protected $_coordinates = null;

    /**
     * Construct
     * 
     * Letter construct declares nothing.
     *
     * You must delcare the coordinates.
     * 
     * @var  array
     */
    public function __construct()
    {}

    /**
     * Sets the draw coordinates for the letter.
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
     * Gets the draw coordinates for the letter.
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
     * Move each letter down x spaces.
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
}