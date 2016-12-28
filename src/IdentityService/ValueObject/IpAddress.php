<?php
namespace IdentityService\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Class IpAddress
 * @package IdentityService\ValueObject
 */
class IpAddress implements JsonSerializable
{
    const INVALID_WARNING = 'Invalid IP address provided.';

    /** @var string */
    private $value;

    /**
     * IpAddress constructor.
     * @param $ipAddress
     * @throws InvalidArgumentException
     */
    private function __construct($ipAddress)
    {
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException(self::INVALID_WARNING);
        }

        if (substr($ipAddress, -2) !== '.0') {
            $packets = explode('.', $ipAddress);
            unset($packets[3]);
            $ipAddress = implode('.', $packets) . '.0';
        }

        $this->value = $ipAddress;
    }

    /**
     * @param $string
     * @return IpAddress
     * @throws InvalidArgumentException
     */
    public static function fromString($string)
    {
        return new IpAddress($string);
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
        return $this->value;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
