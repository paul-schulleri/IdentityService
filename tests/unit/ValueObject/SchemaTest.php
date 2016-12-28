<?php

namespace IdentityService\ValueObject;

/**
 * Class SchemaTest
 * @package IdentityService\ValueObject
 * @covers IdentityService\ValueObject\Schema
 */
class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitialized()
    {
        $object = Schema::fromString('some-name');
        $this->assertInstanceOf(Schema::class, $object);
    }

    public function testCanBeCastedToString()
    {
        $name = 'some-name';
        $object = Schema::fromString($name);
        $this->assertSame($name, $object->toString());
        $this->assertSame($name, (string)$object);
    }

    public function testCanBeGenerated()
    {
        $object = Schema::generate();
        $this->assertInstanceOf(Schema::class, $object);
    }
}

