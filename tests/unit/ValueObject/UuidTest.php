<?php

namespace IdentityService\ValueObject;

use IdentityService\Exception\InvalidUuidException;
use Olando\Helper\ValidatorTrait;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class UuidTest
 * @package olando\ValueObject
 * @covers IdentityService\ValueObject\Uuid
 */
class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitialized()
    {
        $object = Uuid::fromString(RamseyUuid::uuid4());
        $this->assertInstanceOf(Uuid::class, $object);
    }

    public function testCantBeInitializedWithInvalidUuid()
    {
        $this->setExpectedException(InvalidUuidException::class);
        (Uuid::fromString('invalid-uuid'));
    }

    public function testCanGenerateUuid()
    {
        $uuid = Uuid::generate();
        $this->assertTrue(RamseyUuid::isValid($uuid));
    }
}

