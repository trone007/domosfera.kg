<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:35
 */

namespace AppBundle\Image\Geometry\Exceptions;


class UnsupportedFormatException extends \Exception
{

    public function __construct($format)
    {
        $this->message = "This image format ($format) is not supported by your version of GD library";
    }
}