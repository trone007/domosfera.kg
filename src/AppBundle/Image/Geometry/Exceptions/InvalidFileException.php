<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:35
 */

namespace AppBundle\Image\Geometry\Exceptions;


class InvalidFileException extends \Exception
{

    public function __construct($path)
    {
        $this->message = "Invalid file: $path";
    }
}