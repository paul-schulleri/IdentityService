<?php
namespace IdentityService\ValueObject\Website;

use JsonSerializable;

/**
 * Class WebsiteProperties
 * @package IdentityService\ValueObject
 */
class TestParameter implements JsonSerializable
{
    /** @var string */
    private $value;

    /**
     * TestProperty constructor.
     * @param $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $value
     * @return TestParameter
     */
    public static function fromString($value)
    {
        return new TestParameter($value);
    }

    /**
     * @param TestParameter $property
     * @return bool
     */
    public function equals(TestParameter $property)
    {
        return $property->getValue() === $this->getValue();
    }

    /**
     * @param TestParameter $property
     */
    public function mergeValueFromParameter(TestParameter $property)
    {
        $currentValues = explode(',', $this->getValue());
        $incomingValues = explode(',', $property->getValue());
        $newValues = array_unique(array_merge($currentValues, $incomingValues));

        $this->value = implode(',', $newValues);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->value);
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
    public function toString()
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
