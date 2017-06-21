<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine для
 * работы с таблицей companion_group БД.
 *
 *
 * Хранение данных состава коллекций, связана с сущностью Companion по Коду
 */
class CompanionGroup
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
     * @var \AppBundle\Entity\Companion
     */
    private $companionCode;


    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return CompanionGroup
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
     * @return CompanionGroup
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
     * Set companionCode
     *
     * @param \AppBundle\Entity\Companion $companionCode
     *
     * @return CompanionGroup
     */
    public function setCompanionCode(\AppBundle\Entity\Companion $companionCode = null)
    {
        $this->companionCode = $companionCode;

        return $this;
    }

    /**
     * Get companionCode
     *
     * @return \AppBundle\Entity\Companion
     */
    public function getCompanionCode()
    {
        return $this->companionCode;
    }
}
