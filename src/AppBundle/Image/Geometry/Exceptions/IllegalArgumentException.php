<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:33
 */

namespace AppBundle\Image\Geometry\Exceptions;

class IllegalArgumentException extends \Exception
{
    public function __construct()
    {
        $this->message = "Illegal argument";
    }
}