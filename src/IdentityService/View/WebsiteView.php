<?php
namespace IdentityService\View;

use IdentityService\Model\IdentityModel;
use IdentityService\ValueObject\Address;
use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\Country;
use IdentityService\ValueObject\Device;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\Location;
use IdentityService\ValueObject\Referrer;
use Olando\Http\ParameterContainer;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class Website
 * @package IdentityService\View
 */
class WebsiteView implements ViewInterface
{
    /** @var IdentityModel */
    private $identity;

    /**
     * @param $identity
     */
    public function __construct(IdentityModel $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return Location
     * @throws \InvalidArgumentException
     */
    private function getLatestLocation()
    {
        $location = $this->identity->getLatestLocation();
        if (!$location instanceof Location) {
            $location = Location::createFromLocaleAndCountry(
                Locale::fromIsoString('de_DE'),
                Country::fromIsoAndName('de', 'Germany')
            );
        }

        return $location;
    }

    /**
     * @return Address
     */
    private function getLatestAddress()
    {
        $address = null;
        $location = $this->identity->getLatestLocation();
        if ($location instanceof Location) {
            $address = $location->getAddress();
            if (!($address instanceof Address)) {
                $address = Address::fromZipCodeAndCityAndRegion('', '', '');
            }
        }

        return $address;
    }

    /**
     * @return Campaign|null
     */
    private function getLatestCampaign()
    {
        return $this->identity->getLatestCampaign();
    }

    /**
     * @return Device
     * @throws \InvalidArgumentException
     */
    private function getLatestDevice()
    {
        $device = $this->identity->getLatestDevice();
        if (!($device instanceof Device)) {
            $device = Device::fromParameterContainer($this->getEmptyParameterContainer());
        }

        return $device;
    }

    /**
     * @return Referrer
     */
    private function getLatestReferrer()
    {
        $referrer = $this->identity->getLatestReferrer();
        if (!($referrer instanceof Referrer)) {
            $referrer = Referrer::fromParameterContainer($this->getEmptyParameterContainer());
        }

        return $referrer;
    }

    /**
     * @return ParameterContainer
     */
    private function getEmptyParameterContainer()
    {
        return ParameterContainer::fromParameterBag(new ParameterBag());
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $campaign = null;
        $telephoneNumber = null;
        $act = null;
        $browser = null;
        $location = null;
        $country = null;
        $countryName = null;
        $ipAddress = null;

        //
        $latestCampaign = $this->getLatestCampaign();
        if ($latestCampaign instanceof Campaign) {
            $campaign = [
                'comment' => $latestCampaign->getComment(),
                'usageName' => $latestCampaign->getUsageName(),
                'marketingChannelName' => $latestCampaign->getMarketingChannelName(),
                'controllingChannelName' => $latestCampaign->getControllingChannelName(),
                'marketingOfferType' => $latestCampaign->getMarketingOfferType(),
            ];
            $telephoneNumber = $latestCampaign->getTelephoneNumber();
            $act = $latestCampaign->getAct();
        }

        //
        $device = $this->getLatestDevice();
        if ($device instanceof Device) {
            $browser = [
                'userAgent' => $device->getUserAgentString(),
                'name' => $device->getUserAgentName(),
                'version' => $device->getUserAgentVersion(),
                'platform' => $device->getOperatingSystem(),
                'pattern' => null,
                'mobile' => $device->getIsMobile(),
            ];
        }

        //
        $latestLocation = $this->getLatestLocation();
        if ($latestLocation instanceof Location) {
            $countryName = $latestLocation->getCountry()->getName();
            $ipAddress = $latestLocation->getIpAddress();
            $country = $latestLocation->getLocale();
        }

        //
        $address = $this->getLatestAddress();

        if ($address instanceof Address) {
            $location = [
                'city_param' => $address->getCity(),
                'region' => $address->getRegion(),
                'country' => $countryName,
            ];
        }


        $referrerAndTargetUrl = $this->getLatestReferrer();

        if ($referrerAndTargetUrl instanceof Referrer) {
            $referrerUrl = $referrerAndTargetUrl->getReferrerUrl();
            $targetUrl = $referrerAndTargetUrl->getTargetUrl();
        }

        return [
            'site' => [
                'country' => $country,
            ],
            'phonenumber' => $telephoneNumber,
            'leadid' => $this->identity->getLatestLeadId(),
            'id' => $this->identity->getCookieId(),
            'newsletter' => null,
            'act' => $act,
            'referrer' => !empty($referrerUrl) ? $referrerUrl : null,
            'referrer_target' => !empty($targetUrl) ? $targetUrl : null,
            'timestamp' => time(),
            'getvars' => [
                't' => $this->identity->getWebsiteTestParameter(),
            ],
            'browser' => $browser,
            'location' => $location,
            'campaign' => $campaign,
            'ip' => $ipAddress,
        ];
    }
}
