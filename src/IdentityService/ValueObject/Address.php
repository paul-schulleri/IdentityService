<?php
namespace IdentityService\ValueObject;

use JsonSerializable;

/**
 * Class Address
 * @package IdentityService\ValueObject
 */
class Address implements JsonSerializable
{
    /** @var string */
    private $zipCode;

    /** @var string */
    private $city;

    /** @var string */
    private $region;

    /**
     * Address constructor.
     * @param $zipCode
     * @param $city
     * @param $region
     */
    private function __construct($zipCode, $city, $region)
    {
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->region = $region;
    }

    /**
     * @param $zipCode
     * @param $city
     * @param $region
     * @return Address
     */
    public static function fromZipCodeAndCityAndRegion($zipCode, $city, $region)
    {
        return new Address($zipCode, $city, $region);
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getZipCode() . ' ' . $this->getRegion() . ', ' . $this->getCity();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
