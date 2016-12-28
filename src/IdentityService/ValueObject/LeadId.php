<?php
namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidUuidException;
use JsonSerializable;
use OutOfBoundsException;

/**
 * Class LeadId
 * @package IdentityService\ValueObject
 */
class LeadId implements JsonSerializable
{
    /** @var string */
    private $id;

    /**
     * LeadId constructor.
     * @param $id
     */
    private function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param Locale $locale
     * @param IdentityId $identityId
     * @return LeadId
     * @throws OutOfBoundsException
     */
    public static function fromLocaleAndIdentityId(Locale $locale, IdentityId $identityId)
    {
        return new self(
            Locale::getPrefix($locale->toString()) . '-' . $identityId->toString()
        );
    }

    /**
     * @param Locale $locale
     * @return LeadId
     * @throws OutOfBoundsException
     * @throws InvalidUuidException
     */
    public static function fromLocale(Locale $locale)
    {
        return new self(
            Locale::getPrefix($locale->toString()) . '-' . Uuid::generate()
        );
    }

    /**
     * @param $id
     * @return LeadId
     */
    public static function fromString($id)
    {
        return new self($id);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->id ? false : true;
    }

    /**
     * @param LeadId $id
     * @return bool
     */
    public function equals(LeadId $id)
    {
        return $this == $id;
    }
}
