<?php

namespace AppBundle\Entity;


/**
 * Сущность Doctrine для
 * работы с таблицей notebook_group БД.
 *
 * Хранение данных состава коллекций, связана с сущностью Notebook по Коду
 */
class NotebookGroup
{
    /**
     * @var string
     */
    private $vendorCode;

    /**
     * @var string
     */
    private $collectionCode;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Notebook
     */
    private $companion;


    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return NotebookGroup
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * Set collectionCode
     *
     * @param string $collectionCode
     *
     * @return NotebookGroup
     */
    public function setCollectionCode($collectionCode)
    {
        $this->collectionCode = $collectionCode;

        return $this;
    }

    /**
     * Get collectionCode
     *
     * @return string
     */
    public function getCollectionCode()
    {
        return $this->collectionCode;
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
     * Set companion
     *
     * @param \AppBundle\Entity\Notebook $companion
     *
     * @return NotebookGroup
     */
    public function setCompanion(\AppBundle\Entity\Notebook $companion = null)
    {
        $this->companion = $companion;

        return $this;
    }

    /**
     * Get companion
     *
     * @return \AppBundle\Entity\Notebook
     */
    public function getCompanion()
    {
        return $this->companion;
    }
}
