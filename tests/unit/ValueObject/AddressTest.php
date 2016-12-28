<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class AddressTest
 * @covers IdentityService\ValueObject\Address
 * @package olando
 */
class AddressTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $address = Address::fromZipCodeAndCityAndRegion('12345', 'berlin', 'berlin');
        $this->assertInstanceOf(Address::class, $address);
    }

    public function testCanGetPropertyValues()
    {
        $address = Address::fromZipCodeAndCityAndRegion('12345', 'berlin', 'berlin');
        $this->assertSame($address->getCity(), 'berlin');
        $this->assertSame($address->getRegion(), 'berlin');
        $this->assertSame($address->getZipCode(), '12345');
    }

    public function testCanSerializeToJson()
    {
        $object = Address::fromZipCodeAndCityAndRegion('12345', 'berlin', 'berlin');
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue($jsonObject->zipCode == $object->getZipCode());
    }

    public function testCanCastToString()
    {
        /** @var Address $object */
        $object = Address::fromZipCodeAndCityAndRegion('12345', 'berlin', 'berlin');
        $this->assertSame('12345 berlin, berlin', (string)$object);
        $this->assertSame('12345 berlin, berlin', $object->toString());
    }
}
