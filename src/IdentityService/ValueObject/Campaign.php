<?php
namespace IdentityService\ValueObject;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Class Campaign
 * @package IdentityService\ValueObject
 */
class Campaign implements JsonSerializable
{
    /** @var string */
    private $act;

    /** @var string */
    private $quality;

    /** @var string */
    private $comment;

    /** @var string */
    private $usageName;

    /** @var string */
    private $marketingChannelName;

    /** @var string */
    private $controllingChannelName;

    /** @var string */
    private $telephoneNumber;

    /** @var string */
    private $marketingOfferType;

    /**
     * Campaign constructor.
     * @param $act
     * @throws InvalidArgumentException
     */
    private function __construct($act)
    {
        if (empty($act)) {
            throw new InvalidArgumentException('Parameter \'act\' is not set.');
        }

        $this->act = $act;
    }

    /**
     * @param CampaignApiResponse $apiResponse
     * @return Campaign
     * @throws InvalidArgumentException
     */
    public static function fromCampaignApiResponse(CampaignApiResponse $apiResponse)
    {
        $campaign = new Campaign($apiResponse->getAct());

        $campaign->quality = $apiResponse->getQuality();
        $campaign->comment = $apiResponse->getComment();
        $campaign->usageName = $apiResponse->getUsageName();
        $campaign->marketingChannelName = $apiResponse->getMarketingChannelName();
        $campaign->controllingChannelName = $apiResponse->getControllingChannelName();
        $campaign->telephoneNumber = $apiResponse->getTelephoneNumber();
        $campaign->marketingOfferType = $apiResponse->getMarketingOfferType();

        return $campaign;
    }

    /**
     * @param Campaign $campaign
     * @return bool
     */
    public function equals(Campaign $campaign)
    {
        return $this == $campaign;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getAct()
    {
        return $this->act;
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
    public function getComment()
    {
        return $this->comment;
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
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @return string
     */
    public function getMarketingOfferType()
    {
        return $this->marketingOfferType;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        foreach (get_object_vars($this) as $property => $value) {
            if ($property !== 'act' && !empty($value)) {
                return false;
            }
        }

        return true;
    }
}
