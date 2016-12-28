<?php
namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 * @package IdentityService\ValueObject
 */
class Uuid
{
    const INVALID_WARNING = 'UUID not valid';

    /** @var string */
    private $uuid;

    /**
     * Uuid constructor.
     * @param $uuid
     * @throws InvalidUuidException
     */
    private function __construct($uuid)
    {
        if (!RamseyUuid::isValid($uuid)) {
            throw new InvalidUuidException(self::INVALID_WARNING);
        }

        $this->uuid = $uuid;
    }

    /**
     * @param $uuid
     * @return Uuid
     * @throws InvalidUuidException
     */
    public static function fromString($uuid)
    {
        return new Uuid($uuid);
    }

    /**
     * @return Uuid
     * @throws InvalidUuidException
     */
    public static function generate()
    {
        return new Uuid(RamseyUuid::uuid4()->toString());
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
        return $this->uuid;
    }
}
