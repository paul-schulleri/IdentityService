<?php
namespace IdentityService\Model;

use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\CookieId;
use IdentityService\ValueObject\Device;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\LeadId;
use IdentityService\ValueObject\Location;
use IdentityService\ValueObject\Referrer;
use IdentityService\ValueObject\SalesForceLead;
use IdentityService\ValueObject\Website\TestParameter;
use JsonSerializable;

/**
 * Class Identity
 * @package IdentityService\Model
 */
class IdentityModel implements JsonSerializable
{
    /** @var IdentityId */
    private $identityId;

    /** @var TestParameter[] */
    private $websiteTestParameter = [];

    /** @var Campaign[] */
    private $campaigns = [];

    /** @var Location[] */
    private $locations = [];

    /** @var Device[] */
    private $devices = [];

    /** @var Referrer[] */
    private $referrers = [];

    /** @var SalesForceLead[] */
    private $salesForceLeads = [];

    /** @var  CookieId */
    private $cookieId;

    /** @var LeadId[] */
    private $leadIds = [];

    /**
     * IdentityModel constructor.
     * @param IdentityId $identityId
     */
    public function __construct(IdentityId $identityId)
    {
        $this->identityId = $identityId;
    }

    /**
     * @return Campaign[]
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * @return Device[]
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @return Location[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @return Referrer[]
     */
    public function getReferrers()
    {
        return $this->referrers;
    }

    /**
     * @return SalesForceLead[]
     */
    public function getSalesForceLeads()
    {
        return $this->salesForceLeads;
    }

    /**
     * @return LeadId[]
     */
    public function getLeadIds()
    {
        return $this->leadIds;
    }

    /**
     * @return CookieId
     */
    public function getCookieId()
    {
        return $this->cookieId;
    }

    /**
     * @param CookieId $cookieId
     */
    public function addCookieId(CookieId $cookieId)
    {
        $this->cookieId = $cookieId;
    }

    /**
     * @return TestParameter[]
     */
    public function getWebsiteTestParameter()
    {
        return $this->websiteTestParameter;
    }

    /**
     * @param TestParameter $testParameter
     */
    public function addWebsiteTestParameter(TestParameter $testParameter)
    {
        if (!$testParameter->isEmpty()) {
            if ($this->websiteTestParameter instanceof TestParameter) {
                $this->websiteTestParameter->mergeValueFromParameter($testParameter);
            } else {
                $this->websiteTestParameter = $testParameter;
            }
        }
    }

    /**
     * @param SalesForceLead $newLead
     */
    public function addSalesForceLead(SalesForceLead $newLead)
    {
        if (!$newLead->isEmpty()) {
            $existingLead = $this->getLatestSalesForceLead();
            if (!($existingLead instanceof SalesForceLead) || !$existingLead->equals($newLead)) {
                $this->salesForceLeads[$this->getNewElementKey()] = $newLead;
            }
        }
    }

    /**
     * @param Device $newDevice
     */
    public function addDevice(Device $newDevice)
    {
        if (!$newDevice->isEmpty()) {
            $latest = $this->getLatestDevice();
            if (!($latest instanceof Device) || !$latest->equals($newDevice)) {
                $this->devices[$this->getNewElementKey()] = $newDevice;
            }
        }
    }

    /**
     * @param LeadId $newLeadId
     */
    public function addLeadId(LeadId $newLeadId)
    {
        if (!$newLeadId->isEmpty()) {
            $latest = $this->getLatestLeadId();
            if (!($latest instanceof LeadId) || !$latest->equals($newLeadId)) {
                $this->leadIds[$this->getNewElementKey()] = $newLeadId;
            }
        }
    }

    /**
     * @param Campaign $newCampaign
     */
    public function addCampaign(Campaign $newCampaign)
    {
        if (!$newCampaign->isEmpty()) {
            $latest = $this->getLatestCampaign();
            if (!($latest instanceof Campaign) || !$latest->equals($newCampaign)) {
                $this->campaigns[$this->getNewElementKey()] = $newCampaign;
            }
        }
    }

    /**
     * @param Location $newLocation
     */
    public function addLocation(Location $newLocation)
    {
        $latest = $this->getLatestLocation();
        if (!($latest instanceof Location) || !$latest->equals($newLocation)) {
            $this->locations[$this->getNewElementKey()] = $newLocation;
        }
    }

    /**
     * @param Referrer $newReferrer
     */
    public function addReferrer(Referrer $newReferrer)
    {
        if (!$newReferrer->isEmpty()) {
            $latest = $this->getLatestReferrer();
            if (!($latest instanceof Referrer) || !$latest->equals($newReferrer)) {
                $this->referrers[$this->getNewElementKey()] = $newReferrer;
            }
        }
    }

    /**
     * @return IdentityId
     */
    public function getId()
    {
        return $this->identityId;
    }

    /**
     * @param IdentityModel $mergeIdentity
     * @return IdentityModel
     */
    public function mergeIdentity(IdentityModel $mergeIdentity)
    {
        foreach ($mergeIdentity->getLocations() as $location) {
            $this->addLocation($location);
        }

        foreach ($mergeIdentity->getDevices() as $device) {
            $this->addDevice($device);
        }

        foreach ($mergeIdentity->getCampaigns() as $campaign) {
            $this->addCampaign($campaign);
        }

        foreach ($mergeIdentity->getReferrers() as $referrer) {
            $this->addReferrer($referrer);
        }

        foreach ($mergeIdentity->getSalesForceLeads() as $salesForceLead) {
            $this->addSalesForceLead($salesForceLead);
        }

        foreach ($mergeIdentity->getLeadIds() as $leadId) {
            $this->addLeadId($leadId);
        }

        $websiteTestParameter = $mergeIdentity->getWebsiteTestParameter();
        if ($websiteTestParameter instanceof TestParameter) {
            $this->addWebsiteTestParameter($websiteTestParameter);
        }

        $this->cookieId = $this->updateCookieId($mergeIdentity);

        return $this;
    }

    /**
     * @param IdentityModel $mergeIdentity
     * @return CookieId
     */
    private function updateCookieId(IdentityModel $mergeIdentity)
    {
        $incomingCookieId = $mergeIdentity->getCookieId();
        if (null !== $incomingCookieId) {
            return $incomingCookieId;
        }

        return $this->cookieId;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'locations' => $this->getLocations(),
            'campaigns' => $this->getCampaigns(),
            'devices' => $this->getDevices(),
            'referrers' => $this->getReferrers(),
            'websiteTestParameter' => $this->getWebsiteTestParameter(),
            'leadid' => $this->getLeadIds(),
            'external' => [
                'salesforce' => $this->getSalesForceLeads(),
            ],
        ];
    }

    /**
     * @return string
     */
    private function getNewElementKey()
    {
        return (string)microtime(true);
    }

    /**
     * @param $actParam
     * @return Campaign|null
     */
    public function getCampaignByAct($actParam)
    {
        if (!empty($actParam)) {
           return null;
        }

        /** @var Campaign $campaign */
        foreach ($this->campaigns as $key => $campaign) {
            $minimumKey = $this->getNewElementKey() - (60 * 60 * 24 * 7);
            if ($key >= $minimumKey && $campaign->getAct() === $actParam) {
                return $campaign;
            }
        }
    }

    /**
     * @return Campaign|null
     */
    public function getLatestCampaign()
    {
        /** @var Campaign $campaign */
        $campaign = end($this->campaigns);
        return $campaign instanceof Campaign ? $campaign : null;
    }

    /**
     * @return SalesForceLead|null
     */
    public function getLatestSalesForceLead()
    {
        /** @var SalesForceLead $salesForceLead */
        $salesForceLead = end($this->salesForceLeads);
        return $salesForceLead instanceof SalesForceLead ? $salesForceLead : null;
    }

    /**
     * @return Location|null
     */
    public function getLatestLocation()
    {
        /** @var Location $location */
        $location = end($this->locations);
        return $location instanceof Location ? $location : null;
    }

    /**
     * @return Referrer|null
     */
    public function getLatestReferrer()
    {
        /** @var Referrer $referrer */
        $referrer = end($this->referrers);
        return $referrer instanceof Referrer ? $referrer : null;
    }

    /**
     * @return Device|null
     */
    public function getLatestDevice()
    {
        /** @var Device $device */
        $device = end($this->devices);
        return $device instanceof Device ? $device : null;
    }

    /**
     * @return LeadId|null
     */
    public function getLatestLeadId()
    {
        /** @var LeadId $leadId */
        $leadId = end($this->leadIds);
        return $leadId instanceof LeadId ? $leadId : null;
    }
}
