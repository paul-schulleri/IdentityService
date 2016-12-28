<?php
namespace IdentityService\ValueObject;

use JsonSerializable;
use Olando\Http\ParameterContainer;

/**
 * Class SalesForceLead
 * @package IdentityService\ValueObject
 */
class SalesForceLead implements JsonSerializable
{
    /** @var array */
    private $data = [];

    /**
     * SalesForceLead constructor.
     * @param array $data
     */
    private function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @return SalesForceLead
     */
    public static function fromParameterContainer(ParameterContainer $parameterContainer)
    {
        return new self($parameterContainer->toArray());
    }

    /**
     * @param array $params
     * @return SalesForceLead
     */
    public static function fromArray(array $params)
    {
        return new self($params);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return json_encode($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param SalesForceLead $salesForceLead
     * @return bool
     */
    public function equals(SalesForceLead $salesForceLead)
    {
        return $this->jsonSerialize() === $salesForceLead->jsonSerialize();
    }
}
