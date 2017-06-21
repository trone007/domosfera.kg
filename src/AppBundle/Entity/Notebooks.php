<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine  для работы с таблицей notebooks БД.
 *
 * Сюда происходит выгрузка всех тетрадей из 1C.
 * Необходима для показа изображений. Логически связана с NotebookImage.
 */
class Notebooks
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
    private $catalogCode;

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
     * @return Notebooks
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
     * @return Notebooks
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
     * Set catalogCode
     *
     * @param string $catalogCode
     *
     * @return Notebooks
     */
    public function setCatalogCode($catalogCode)
    {
        $this->catalogCode = $catalogCode;

        return $this;
    }

    /**
     * Get catalogCode
     *
     * @return string
     */
    public function getCatalogCode()
    {
        return $this->catalogCode;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Notebooks
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
