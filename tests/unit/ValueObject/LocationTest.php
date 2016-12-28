<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;
use const true;

/**
 * Class LocationTest
 * @covers IdentityService\ValueObject\Location
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\Address
 * @uses   IdentityService\ValueObject\IpAddress
 * @uses   IdentityService\ValueObject\Country
 * @package olando
 */
class LocationTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        /** @var Location $object */
        $object = $this->getLocation(true);
        $this->assertInstanceOf(Location::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        /** @var Location $object */
        $object = $this->getLocation(true);
        $this->assertSame('123456 Berlin, Berlin', (string)$object->getAddress());
        $this->assertSame('Germany', $object->getCountry()->getName());
        $this->assertNotEmpty((string)$object->getIpAddress()); // ip address is obfuscated
    }

    public function testCanSerializeToJson()
    {
        /** @var Location $object */
        $object = $this->getLocation(true);
        $jsonObject = json_decode(json_encode($object));
        $this->assertSame($jsonObject->locale, (string)$object->getLocale());
    }

    public function testCanCheckEqualitiy()
    {
        /** @var Location $object */
        $object = $this->getLocation(true);
        $objectSecond = $this->getLocation(true);
        $this->assertTrue($object->equals($objectSecond));
    }

    /**
     * @param $filledWithData
     * @return Location
     */
    private function getLocation($filledWithData)
    {
        if ($filledWithData) {
            // de_DE
            $locale = Locale::fromIsoString('de_DE');
            $country = Country::fromIsoAndName('de', 'Germany');

            /** @var Locale $locale */
            /** @var Country $country */
            $location = Location::createFromLocaleAndCountry($locale, $country);
            $location->setAddress(Address::fromZipCodeAndCityAndRegion('123456', 'Berlin', 'Berlin'));
            $location->setIpAddress(IpAddress::fromString('86.56.5.23'));
        } else {
            // de_DE
            $locale = Locale::fromIsoString('');
            $country = Country::fromIsoAndName('', '');

            $location = Location::createFromLocaleAndCountry($locale, $country);
        }
        return $location;
    }

}
