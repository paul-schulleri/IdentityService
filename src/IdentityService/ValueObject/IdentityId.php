<?php
namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidIdentityIdException;
use IdentityService\Exception\InvalidUuidException;
use JsonSerializable;

/**
 * Class Uuid
 * @package olando
 */
class IdentityId implements JsonSerializable
{
    /** @var Uuid */
    private $uuid;

    /**
     * IdentityId constructor.
     * @param Uuid $uuid
     */
    private function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return IdentityId
     * @throws InvalidUuidException
     */
    public static function generate()
    {
        return new self(Uuid::generate());
    }

    /**
     * @param Uuid $uuid
     * @return IdentityId
     */
    public static function fromUuid(Uuid $uuid)
    {
        return new self($uuid);
    }

    /**
     * @param $uuid
     * @return IdentityId
     * @throws InvalidIdentityIdException
     */
    public static function fromString($uuid)
    {
        try {
            return self::fromUuid(Uuid::fromString($uuid));
        } catch (InvalidUuidException $e) {
            throw new InvalidIdentityIdException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
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
        return (string)$this->uuid->toString();
    }
}
