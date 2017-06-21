<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 24.02.17
 * Time: 13:34
 */

namespace AppBundle\Image\Geometry\Exceptions;


class GDnotInstalledException extends \Exception
{
    public function __construct()
    {
        $this->message = "The GD library is not installed";
    }
}