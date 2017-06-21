<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:35
 */

namespace AppBundle\Image\Geometry\Exceptions;


class FileNotFoundException extends \Exception
{
    const MESSAGE = 'File not found';

    public function __construct()
    {
        $this->message = self::MESSAGE;
    }
}