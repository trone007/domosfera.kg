<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine для
 * работы с таблицей collection_group БД.
 *
 *
 * Хранение данных состава коллекций, связана с сущностью Collection по Коду
 */
class CollectionGroup
{
    /**
     * @var string
     */
    private $vendorCode;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Collection
     */
    private $companion;


    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return CollectionGroup
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
     * @param \AppBundle\Entity\Collection $companion
     *
     * @return CollectionGroup
     */
    public function setCompanion(\AppBundle\Entity\Collection $companion = null)
    {
        $this->companion = $companion;

        return $this;
    }

    /**
     * Get companion
     *
     * @return \AppBundle\Entity\Collection
     */
    public function getCompanion()
    {
        return $this->companion;
    }
}
