<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine для работы с таблицей image БД.
 *
 * Хранение Идентефикаторов из 1C для получения изображений
 * Хранение Самих изображений в формате Blob.
 */
class Images
{
    /**
     * @var string
     */
    private $imageCode;

    /**
     * @var string
     */
    private $image;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set imageCode
     *
     * @param string $imageCode
     *
     * @return Images
     */
    public function setImageCode($imageCode)
    {
        $this->imageCode = $imageCode;

        return $this;
    }

    /**
     * Get imageCode
     *
     * @return string
     */
    public function getImageCode()
    {
        return $this->imageCode;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Images
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return Images
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
