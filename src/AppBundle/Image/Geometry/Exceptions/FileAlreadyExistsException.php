<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:34
 */

namespace AppBundle\Image\Geometry\Exceptions;


class FileAlreadyExistsException extends \Exception
{
    public function __construct($path)
    {
        $this->message = "File $path is already exists!";
    }
}