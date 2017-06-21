<?php

namespace AppBundle\Entity;

/**
 * Сущность Doctrine  для работы с таблицей wallpaper БД.
 *
 * Хранение информации о товарах в магазинах. Поставляется из 1C. При помощи API.
 *
 * @todo переименовать в товары
 */
class Wallpaper
{
    /**
     * @var string
     */
    private $shop;

    /**
     * @var string
     */
    private $organization;

    /**
     * @var string
     */
    private $vendorCode;

    /**
     * @var string
     */
    private $image;

    /**
     * @var boolean
     */
    private $seamlessStructure;

    /**
     * @var string
     */
    private $priceOld;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var string
     */
    private $mainNomenclature;

    /**
     * @var string
     */
    private $nomenclature;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $catalog;

    /**
     * @var string
     */
    private $notebook;

    /**
     * @var boolean
     */
    private $glitter;

    /**
     * @var string
     */
    private $color1;

    /**
     * @var string
     */
    private $color2;

    /**
     * @var string
     */
    private $color3;

    /**
     * @var string
     */
    private $basis;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var string
     */
    private $texture;

    /**
     * @var string
     */
    private $speed;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $manufacturer;

    /**
     * @var string
     */
    private $successfull;

    /**
     * @var string
     */
    private $points;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $size;

    /**
     * @var string
     */
    private $marketPlan;

    /**
     * @var string
     */
    private $collectionCode;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set shop
     *
     * @param string $shop
     *
     * @return Wallpaper
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return string
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set organization
     *
     * @param string $organization
     *
     * @return Wallpaper
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return Wallpaper
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
     * Set image
     *
     * @param string $image
     *
     * @return Wallpaper
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
     * Set seamlessStructure
     *
     * @param boolean $seamlessStructure
     *
     * @return Wallpaper
     */
    public function setSeamlessStructure($seamlessStructure)
    {
        $this->seamlessStructure = $seamlessStructure;

        return $this;
    }

    /**
     * Get seamlessStructure
     *
     * @return boolean
     */
    public function getSeamlessStructure()
    {
        return $this->seamlessStructure;
    }

    /**
     * Set priceOld
     *
     * @param string $priceOld
     *
     * @return Wallpaper
     */
    public function setPriceOld($priceOld)
    {
        $this->priceOld = $priceOld;

        return $this;
    }

    /**
     * Get priceOld
     *
     * @return string
     */
    public function getPriceOld()
    {
        return $this->priceOld;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Wallpaper
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Wallpaper
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return Wallpaper
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set mainNomenclature
     *
     * @param string $mainNomenclature
     *
     * @return Wallpaper
     */
    public function setMainNomenclature($mainNomenclature)
    {
        $this->mainNomenclature = $mainNomenclature;

        return $this;
    }

    /**
     * Get mainNomenclature
     *
     * @return string
     */
    public function getMainNomenclature()
    {
        return $this->mainNomenclature;
    }

    /**
     * Set nomenclature
     *
     * @param string $nomenclature
     *
     * @return Wallpaper
     */
    public function setNomenclature($nomenclature)
    {
        $this->nomenclature = $nomenclature;

        return $this;
    }

    /**
     * Get nomenclature
     *
     * @return string
     */
    public function getNomenclature()
    {
        return $this->nomenclature;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Wallpaper
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set catalog
     *
     * @param string $catalog
     *
     * @return Wallpaper
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
     * Set notebook
     *
     * @param string $notebook
     *
     * @return Wallpaper
     */
    public function setNotebook($notebook)
    {
        $this->notebook = $notebook;

        return $this;
    }

    /**
     * Get notebook
     *
     * @return string
     */
    public function getNotebook()
    {
        return $this->notebook;
    }

    /**
     * Set glitter
     *
     * @param boolean $glitter
     *
     * @return Wallpaper
     */
    public function setGlitter($glitter)
    {
        $this->glitter = $glitter;

        return $this;
    }

    /**
     * Get glitter
     *
     * @return boolean
     */
    public function getGlitter()
    {
        return $this->glitter;
    }

    /**
     * Set color1
     *
     * @param string $color1
     *
     * @return Wallpaper
     */
    public function setColor1($color1)
    {
        $this->color1 = $color1;

        return $this;
    }

    /**
     * Get color1
     *
     * @return string
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * Set color2
     *
     * @param string $color2
     *
     * @return Wallpaper
     */
    public function setColor2($color2)
    {
        $this->color2 = $color2;

        return $this;
    }

    /**
     * Get color2
     *
     * @return string
     */
    public function getColor2()
    {
        return $this->color2;
    }

    /**
     * Set color3
     *
     * @param string $color3
     *
     * @return Wallpaper
     */
    public function setColor3($color3)
    {
        $this->color3 = $color3;

        return $this;
    }

    /**
     * Get color3
     *
     * @return string
     */
    public function getColor3()
    {
        return $this->color3;
    }

    /**
     * Set basis
     *
     * @param string $basis
     *
     * @return Wallpaper
     */
    public function setBasis($basis)
    {
        $this->basis = $basis;

        return $this;
    }

    /**
     * Get basis
     *
     * @return string
     */
    public function getBasis()
    {
        return $this->basis;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Wallpaper
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set texture
     *
     * @param string $texture
     *
     * @return Wallpaper
     */
    public function setTexture($texture)
    {
        $this->texture = $texture;

        return $this;
    }

    /**
     * Get texture
     *
     * @return string
     */
    public function getTexture()
    {
        return $this->texture;
    }

    /**
     * Set speed
     *
     * @param string $speed
     *
     * @return Wallpaper
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return string
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Wallpaper
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
     * Set manufacturer
     *
     * @param string $manufacturer
     *
     * @return Wallpaper
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set successfull
     *
     * @param string $successfull
     *
     * @return Wallpaper
     */
    public function setSuccessfull($successfull)
    {
        $this->successfull = $successfull;

        return $this;
    }

    /**
     * Get successfull
     *
     * @return string
     */
    public function getSuccessfull()
    {
        return $this->successfull;
    }

    /**
     * Set points
     *
     * @param string $points
     *
     * @return Wallpaper
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return Wallpaper
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
     * Set size
     *
     * @param string $size
     *
     * @return Wallpaper
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set marketPlan
     *
     * @param string $marketPlan
     *
     * @return Wallpaper
     */
    public function setMarketPlan($marketPlan)
    {
        $this->marketPlan = $marketPlan;

        return $this;
    }

    /**
     * Get marketPlan
     *
     * @return string
     */
    public function getMarketPlan()
    {
        return $this->marketPlan;
    }

    /**
     * Set collectionCode
     *
     * @param string $collectionCode
     *
     * @return Wallpaper
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
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $style;


    /**
     * Set type
     *
     * @param string $type
     *
     * @return Wallpaper
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set style
     *
     * @param string $style
     *
     * @return Wallpaper
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }
}
