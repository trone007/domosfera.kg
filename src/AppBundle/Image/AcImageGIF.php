<?php
namespace AppBundle\Image;
/**
 * @package image
 *
 * @author Андрей Загорцев <freeron@ya.ru>
 * @author Антон Кургузенков <kurguzenkov@list.ru>
 *
 * @version 0.01
 * @since 2012-11-11
 */

use AppBundle\Image\AcImage;
use AppBundle\Image\Geometry\Exceptions as Exceptions;


/**
 * Класс, описывающий изображение в формате gif
 */

class AcImageGIF extends AcImage
{

	/**
	 * Проверяет, поддерживается ли формат gif
	 *
	 * @return boolean
	 */

	public static function isSupport()
	{
		$gdInfo = parent::getGDinfo();
		return $gdInfo['GIF Read Support'] && $gdInfo['GIF Create Support'];
	}

	/**
	 * @param string путь к файлу с изображением
	 * @throws UnsupportedFormatException
	 */

	protected function __construct($filePath)
	{
		if (!self::isSupport())
			throw new Exceptions\ UnsupportedFormatException('gif');

		parent::__construct($filePath);
		$path = parent::getFilePath();
		parent::setResource(@imagecreatefromgif($path));
	}

	/**
	 * @param string путь, по которому будет сохранено изображение
	 * @return AcImage
	 * @throws FileAlreadyExistsException
	 * @throws FileNotSaveException
	 */

	public function save($path)
	{
		return parent::saveAsGIF($path);
	}
}
?>
