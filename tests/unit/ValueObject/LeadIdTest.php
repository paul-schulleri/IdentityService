<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class LeadIdTest
 * @covers IdentityService\ValueObject\LeadId
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\Uuid
 * @package olando
 */
class LeadIdTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        /** @var LeadId $object */
        $object = $this->getLeadId();
        self::assertInstanceOf(LeadId::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        /** @var LeadId $object */
        $object = $this->getLeadId();
        $leadIdString = 'DEU-0000000-00000-0000-0000000';
        self::assertSame($object->fromString($leadIdString)->toString(), $leadIdString);
        self::assertSame((string)$object->fromString($leadIdString), $leadIdString);
    }

    public function testLeadIdFromLocale()
    {
        /** @var Locale $locale */
        $locale = Locale::fromIsoString('de_DE');
        $leadId = LeadId::fromLocale($locale);

        self::assertInstanceOf(LeadId::class, $leadId);
    }

    public function testCanSerializeToJson()
    {
        /** @var LeadId $object */
        $object = $this->getLeadId();
        $jsonObject = json_decode(json_encode($object));
        self::assertTrue($jsonObject == $object->toString());
    }

    public function testCanCheckEqualitiy()
    {
        $object = $this->getLeadId();
        $objectSecond = $this->getLeadId();
        self::assertTrue($object->equals($objectSecond));
    }

    public function testCanCheckEmptiness()
    {
        $object = $this->getLeadId();
        self::assertFalse($object->isEmpty());
    }

    private function getLeadId()
    {
        // de_DE
        $locale = Locale::fromIsoString('de_DE');
        $identityId = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class);
        /** @var Locale $locale */
        /** @var IdentityId $identityId */
        return LeadId::fromLocaleAndIdentityId($locale, $identityId);
    }
}
