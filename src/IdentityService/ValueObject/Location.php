<?php
namespace IdentityService\ValueObject;

use JsonSerializable;

/**
 * Class Location
 * @package IdentityService\ValueObject
 */
class Location implements JsonSerializable
{
    /** @var Locale */
    private $locale;

    /** @var Country */
    private $country;

    /** @var Address */
    private $address;

    /** @var IpAddress */
    private $ipAddress;

    /**
     * Location constructor.
     * @param Locale $locale
     * @param Country $country
     */
    private function __construct(Locale $locale, Country $country)
    {
        $this->locale = $locale;
        $this->country = $country;
    }

    /**
     * @param Locale $locale
     * @param Country $country
     * @return Location
     */
    public static function createFromLocaleAndCountry(Locale $locale, Country $country)
    {
        return new self($locale, $country);
    }

    /**
     * @param Location $location
     * @return bool
     */
    public function equals(Location $location)
    {
        return $this == $location;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param IpAddress $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }
}
