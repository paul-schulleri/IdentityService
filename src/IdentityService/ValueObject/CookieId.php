<?php
namespace IdentityService\ValueObject;

use JsonSerializable;
use Olando\Http\ParameterContainer;

/**
 * Class CookieId
 * @package IdentityService\ValueObject
 */
class CookieId implements JsonSerializable
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
     * @param ParameterContainer $parameters
     * @return CookieId
     */
    public static function fromParameterContainer(ParameterContainer $parameters)
    {
        $cookieId = $parameters->get(ParameterName::getCookieId());

        if (!empty($cookieId)) {
            return self::fromString(
                $parameters->get(ParameterName::getCookieId())
            );
        }

        return self::fromString(null);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->toString();
    }

    /**
     * @param $id
     * @return CookieId
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
}
