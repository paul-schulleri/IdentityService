<?php
namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;

/**
 * Class CampaignApiResponse
 * @package IdentityService\ValueObject
 */
class CampaignApiResponse
{
    /** @var string */
    private $telephoneNumber;
    /** @var string */
    private $regionName;
    /** @var int */
    private $act;
    /** @var int */
    private $geoZipCode;
    /** @var string */
    private $geoRegion;
    /** @var string */
    private $geoCity;
    /** @var string */
    private $comment;
    /** @var string */
    private $country;
    /** @var string */
    private $quality;
    /** @var string */
    private $usageName;
    /** @var string */
    private $marketingChannelName;
    /** @var string */
    private $controllingChannelName;
    /** @var string */
    private $marketingOfferType;

    /**
     * CampaignApiResponse constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param ParameterContainer $params
     * @return CampaignApiResponse
     */
    public static function fromParameterContainer(ParameterContainer $params)
    {
        $response = new CampaignApiResponse();

        foreach (get_class_vars(self::class) as $field => $value) {
            $paramValue = $params->get($field);
            if (null === $paramValue) {
                $paramValue = $params->get(strtolower($field));
            }
            $response->$field = $paramValue;
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @return string
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @return int
     */
    public function getAct()
    {
        return $this->act;
    }

    /**
     * @return int
     */
    public function getGeoZipCode()
    {
        return $this->geoZipCode;
    }

    /**
     * @return string
     */
    public function getGeoRegion()
    {
        return $this->geoRegion;
    }

    /**
     * @return string
     */
    public function getGeoCity()
    {
        return $this->geoCity;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @return string
     */
    public function getUsageName()
    {
        return $this->usageName;
    }

    /**
     * @return string
     */
    public function getMarketingChannelName()
    {
        return $this->marketingChannelName;
    }

    /**
     * @return string
     */
    public function getControllingChannelName()
    {
        return $this->controllingChannelName;
    }

    /**
     * @return string
     */
    public function getMarketingOfferType()
    {
        return $this->marketingOfferType;
    }
}
