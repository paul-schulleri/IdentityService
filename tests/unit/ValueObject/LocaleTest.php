<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class LocaleTest
 * @covers IdentityService\ValueObject\Locale
 * @package olando
 */
class LocaleTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        /** @var Locale $object */
        $object = Locale::fromIsoString('de_DE');
        $this->assertInstanceOf(Locale::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        /** @var Locale $object */
        $object = Locale::fromIsoString('de_DE');

        $this->assertSame('DE', $object->getCountry());
        $this->assertSame('de', $object->getLanguage());
        $this->assertSame('DEU', $object->getPrefix('de_DE'));
    }

    public function testCanSerializeToJson()
    {
        /** @var Locale $object */
        $object = Locale::fromIsoString('de_DE');
        $jsonObject = json_decode(json_encode($object));
        $this->assertSame('de_DE', $jsonObject);
    }

    public function testCanCastToString()
    {
        /** @var Locale $object */
        $object = Locale::fromIsoString('de_DE');
        $this->assertSame('de_DE', (string)$object);
        $this->assertSame('de_DE', $object->toString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForInvalidLocale()
    {
        /** @var Locale $object */
        Locale::fromIsoString('-invalid-locale-');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForWrongPlacedDashInLocale()
    {
        /** @var Locale $object */
        Locale::fromIsoString('deD_E');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsExceptionForUnknownPrefix()
    {
        /** @var Locale $object */
        Locale::getPrefix('-invalid-locale-');
    }

    public function testCanCheckEqualitiy()
    {
        /** @var Locale $object */
        $object = Locale::fromIsoString('de_DE');
        $objectSecond = Locale::fromIsoString('de_DE');
        $this->assertTrue($object->equals($objectSecond));
    }
}
