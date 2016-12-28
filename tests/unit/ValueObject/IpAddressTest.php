<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class IpAddressTest
 * @covers IdentityService\ValueObject\IpAddress
 * @uses   IdentityService\ValueObject\Locale
 * @package olando
 */
class IpAddressTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = IpAddress::fromString('172.0.0.1');
        $this->assertInstanceOf(IpAddress::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        $object = IpAddress::fromString('172.0.0.1');
        $ipAddressString = '172.0.0.1';
        $this->assertNotEmpty($object->fromString($ipAddressString)->toString());
    }

    public function testCanObfuscateIpAddress()
    {
        $object = IpAddress::fromString('172.0.0.1');
        $this->assertSame('172.0.0.0', $object->fromString('172.0.0.1')->toString());
    }

    public function testCanCastToString()
    {
        /** @var Locale $object */
        $object = IpAddress::fromString('172.0.0.1');
        $this->assertSame('172.0.0.0', (string)$object);
        $this->assertSame('172.0.0.0', $object->toString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForWrongPlacedDashInLocale()
    {
        IpAddress::fromString('-invalid-address-');
    }

    public function testCanSerializeToJson()
    {
        /** @var IpAddress $object */
        $object = IpAddress::fromString('172.0.0.1');
        $jsonObject = json_decode(json_encode($object));
        $this->assertSame($object->toString(), $jsonObject);
    }
}
