<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class CountryTest
 * @covers IdentityService\ValueObject\Country
 * @package olando
 */
class CountryTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = Country::fromIsoAndName('iso', 'name');
        $this->assertInstanceOf(Country::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        $object = Country::fromIsoAndName('iso', 'name');
        $this->assertSame($object->getIso(), 'iso');
        $this->assertSame($object->getName(), 'name');
    }

    public function testCanSerializeToJson()
    {
        $object = Country::fromIsoAndName('iso', 'name');
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue($jsonObject->iso == $object->getIso());
    }
}
