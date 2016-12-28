<?php
namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidSchemaException;

/**
 * Class Schema
 * @package IdentityService\ValueObject
 */
class Schema
{
    /** @var string */
    private $name;

    /**
     * Schema constructor.
     * @param $name
     * @throws InvalidSchemaException
     */
    private function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param $name
     * @return Schema
     * @throws InvalidSchemaException
     */
    public static function fromString($name)
    {
        return new Schema($name);
    }

    /**
     * @throws InvalidSchemaException
     */
    public static function generate()
    {
        return new Schema(microtime());
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
        return $this->name;
    }
}
