<?php
namespace IdentityService\ValueObject;

use JsonSerializable;

/**
 * Class Country
 * @package IdentityService\ValueObject
 */
class Country implements JsonSerializable
{
    /** @var string */
    private $iso;

    /** @var string */
    private $name;

    /**
     * Country constructor.
     * @param $iso
     * @param $name
     */
    private function __construct($iso, $name)
    {
        $this->iso = $iso;
        $this->name = $name;
    }

    /**
     * @param $iso
     * @param $name
     * @return Country
     */
    public static function fromIsoAndName($iso, $name)
    {
        return new Country($iso, $name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
