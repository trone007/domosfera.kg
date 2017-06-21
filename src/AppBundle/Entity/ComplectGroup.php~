<?php

namespace AppBundle\Entity;


/**
 * Сущность Doctrine для
 * работы с таблицей complect_group БД.
 *
 *
 * Хранение данных состава коллекций, связана с сущностью Complect по Коду
 */
class ComplectGroup
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
     * @var \AppBundle\Entity\Complect
     */
    private $complectCode;


    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return ComplectGroup
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
     * @return ComplectGroup
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
     * Set complectCode
     *
     * @param \AppBundle\Entity\Complect $complectCode
     *
     * @return ComplectGroup
     */
    public function setComplectCode(\AppBundle\Entity\Complect $complectCode = null)
    {
        $this->complectCode = $complectCode;

        return $this;
    }

    /**
     * Get complectCode
     *
     * @return \AppBundle\Entity\Complect
     */
    public function getComplectCode()
    {
        return $this->complectCode;
    }
}
