<?php

namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidIdentityIdException;
use PHPUnit_Framework_TestCase;

/**
 * Class IdentityIdTest
 * @covers IdentityService\ValueObject\IdentityId
 * @uses   IdentityService\ValueObject\Uuid
 * @package olando
 */
class IdentityIdTest extends PHPUnit_Framework_TestCase
{
    public function testCanGenerateIdentityId()
    {
        $this->assertInstanceOf(IdentityId::class, IdentityId::generate());
    }

    public function testCanCreateIdentityIdFromUuidString()
    {
        $uuid = '550e8400-e29b-11d4-a716-446655440000';
        $this->assertInstanceOf(IdentityId::class, IdentityId::fromString($uuid));
        $this->assertSame($uuid, IdentityId::fromString($uuid)->toString());
        $this->assertTrue(is_string((string)IdentityId::generate()));
    }

    /**
     * @expectedException \IdentityService\Exception\InvalidIdentityIdException
     * @throws InvalidIdentityIdException
     */
    public function testHandlesInvalidUuidString()
    {
        IdentityId::fromString('invalid-uuid-string');
    }

    public function testCanSerializeToJson()
    {
        $uuid = '550e8400-e29b-11d4-a716-446655440000';
        $object = IdentityId::fromString($uuid);
        $jsonObject = json_decode(json_encode($object));
        $this->assertSame($uuid, $jsonObject);
    }
}
