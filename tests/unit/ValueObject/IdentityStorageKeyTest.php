<?php

namespace IdentityService\ValueObject;

/**
 * Class IdentityStorageKeyTest
 * @package IdentityService\ValueObject
 * @covers IdentityService\ValueObject\IdentityStorageKey
 */
class IdentityStorageKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitialized()
    {
        $object = IdentityStorageKey::fromId($this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class));
        $this->assertInstanceOf(IdentityStorageKey::class, $object);
    }

    public function testCanBeCastedToString()
    {
        $object = IdentityStorageKey::fromId($this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class));
        $this->assertStringStartsWith('identity_', $object->toString());
        $this->assertStringStartsWith('identity_', (string)$object);
    }
}

