<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine для
 * работы с таблицей notebook БД.
 *
 * Хранение данных наименований тетрадей
 */
class Notebook
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $catalog;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Notebook
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
     * Set catalog
     *
     * @param string $catalog
     *
     * @return Notebook
     */
    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;

        return $this;
    }

    /**
     * Get catalog
     *
     * @return string
     */
    public function getCatalog()
    {
        return $this->catalog;
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
    /**
     * @var string
     */
    private $property;


    /**
     * Set property
     *
     * @param string $property
     *
     * @return Notebook
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
    /**
     * @var string
     */
    private $propertySecond;


    /**
     * Set propertySecond
     *
     * @param string $propertySecond
     *
     * @return Notebook
     */
    public function setPropertySecond($propertySecond)
    {
        $this->propertySecond = $propertySecond;

        return $this;
    }

    /**
     * Get propertySecond
     *
     * @return string
     */
    public function getPropertySecond()
    {
        return $this->propertySecond;
    }
}
