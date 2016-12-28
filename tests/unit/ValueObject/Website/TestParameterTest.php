<?php

namespace IdentityService\ValueObject\Website;

use PHPUnit_Framework_TestCase;

/**
 * Class TestParameterTest
 * @package IdentityService\ValueObject\Website
 * @covers \IdentityService\ValueObject\Website\TestParameter
 */
class TestParameterTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('t');
        self::assertInstanceOf(TestParameter::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('t');
        self::assertSame('t', $object->getValue());
    }

    public function testCanSerializeToJson()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('t');
        $jsonObject = json_decode(json_encode($object));
        self::assertSame('t', $jsonObject);
    }

    public function testCanCastToString()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('t');
        self::assertSame('t', (string)$object);
        self::assertSame('t', $object->toString());
    }

    public function testCanCheckEmptiness()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('');

        self::assertTrue($object->isEmpty());
    }

    public function testCanMergeValueFromParameter()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('a');
        $objectMerge = TestParameter::fromString('b');
        $object->mergeValueFromParameter($objectMerge);
        self::assertSame('a,b', $object->getValue());
    }

    public function testCanMergeValueFromParameterEqualParameter()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('a');
        $objectMerge = TestParameter::fromString('EqualParameter');
        $objectThird = TestParameter::fromString('EqualParameter');

        $object->mergeValueFromParameter($objectMerge);
        $object->mergeValueFromParameter($objectThird);

        self::assertSame('a,EqualParameter', $object->getValue());
    }

    public function testCanCheckEqualitiy()
    {
        /** @var TestParameter $object */
        $object = TestParameter::fromString('t');
        $objectSecond = TestParameter::fromString('t');
        self::assertTrue($object->equals($objectSecond));
    }
}
