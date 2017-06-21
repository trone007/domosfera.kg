<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine для
 * работы с таблицей catalog БД.
 *
 *
 * Хранение данных обо всех каталогах приходящих из 1С
 */
class Catalogs
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $image;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Catalogs
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Catalogs
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Catalogs
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
