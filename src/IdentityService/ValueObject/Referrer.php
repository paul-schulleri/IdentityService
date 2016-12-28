<?php
namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;

/**
 * Class Referrer
 * @package IdentityService\ValueObject
 */
class Referrer implements \JsonSerializable
{
    /** @var string */
    private $referrerUrl;

    /** @var string */
    private $targetUrl;

    /**
     * Referrer constructor.
     * @param $referrerUrl
     * @param $targetUrl
     */
    private function __construct($referrerUrl, $targetUrl)
    {
        $this->referrerUrl = $referrerUrl;
        $this->targetUrl = $targetUrl;
    }

    /**
     * @param $referrerUrl
     * @param $targetUrl
     * @return Referrer
     */
    public static function fromReferrerAndTargetUrl($referrerUrl, $targetUrl)
    {
        return new self($referrerUrl, $targetUrl);
    }

    /**
     * @param ParameterContainer $parameters
     * @return Referrer|null
     */
    public static function fromParameterContainer(ParameterContainer $parameters)
    {
        $referrerUrl = $parameters->get(ParameterName::getReferrer());
        $targetUrl = $parameters->get(ParameterName::getTargetUrl());

        if (!empty($referrerUrl) && $referrerUrl !== $targetUrl) {
            return self::fromReferrerAndTargetUrl($referrerUrl, $targetUrl);
        }

        return self::fromReferrerAndTargetUrl('', '');
    }

    /**
     * @param Referrer $referrer
     * @return bool
     */
    public function equals(Referrer $referrer)
    {
        return $this == $referrer;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->referrerUrl);
    }

    /**
     * @return string
     */
    public function getReferrerUrl()
    {
        return $this->referrerUrl;
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }
}
