<?php

namespace IdentityService\View;

use IdentityService\Model\IdentityModel;
use IdentityService\ValueObject\Address;
use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\Country;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\Location;
use PHPUnit_Framework_TestCase;
use stdClass;
use const true;

/**
 * Class WebsiteViewTest
 * @covers IdentityService\View\WebsiteView
 * @uses   IdentityService\Model\IdentityModel
 * @uses   IdentityService\ValueObject\Location
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\LeadId
 * @uses   IdentityService\ValueObject\Country
 * @uses   IdentityService\ValueObject\ParameterName
 * @uses   IdentityService\ValueObject\Referrer
 * @uses   IdentityService\ValueObject\Address
 * @uses   IdentityService\ValueObject\Device
 * @package olando
 */
class WebsiteViewTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = new WebsiteView($this->getIdentity(true));
        $this->assertInstanceOf(WebsiteView::class, $object);
    }

    public function testCanSerializeToJson()
    {
        $object = new WebsiteView($this->getIdentity(true));
        $jsonObject = json_decode(json_encode($object));
        $this->assertInstanceOf(stdClass::class, $jsonObject->site);
    }

    public function testCanSerializeToJsonWithEmptyObjects()
    {
        $object = new WebsiteView($this->getIdentity(false));
        $jsonObject = json_decode(json_encode($object));
        $this->assertInstanceOf(stdClass::class, $jsonObject->site);
    }

    /**
     * @param $hasObjects
     * @return IdentityModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getIdentity($hasObjects)
    {

        $locale = $this->getMockWithoutInvokingTheOriginalConstructor(Locale::class);
        $country = $this->getMockWithoutInvokingTheOriginalConstructor(Country::class);
        $address = $this->getMockWithoutInvokingTheOriginalConstructor(Address::class);
        $campaign = $this->getMockWithoutInvokingTheOriginalConstructor(Campaign::class);
        $location = $this->getMockWithoutInvokingTheOriginalConstructor(Location::class);
        $location->method('getLocale')->willReturn($locale);
        $location->method('getCountry')->willReturn($country);
        $identityModel = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
        if ($hasObjects) {
            $identityModel->method('getLatestLocation')->willReturn($location);
            $identityModel->method('getLatestCampaign')->willReturn($campaign);
            $identityModel->method('getLatestAddress')->willReturn($address);
        }
        /** @var IdentityModel $identityModel */
        return $identityModel;
    }
}
