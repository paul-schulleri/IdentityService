<?php

namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class LeadIdTest
 * @covers IdentityService\ValueObject\CookieId
 * @uses   IdentityService\ValueObject\ParameterName
 * @package olando
 */
class CookieIdTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        /** @var CookieId $object */
        $object = $this->getCookieId('cookie-123');
        self::assertInstanceOf(CookieId::class, $object);
    }

    public function testCookieIdNull()
    {
        /** @var CookieId $object */
        $object = $this->getCookieId(null);
        self::assertNull($object->getId());
    }

    public function testCanGetPropertyValues()
    {
        $cookieIdString = 'cookie-123';
        /** @var CookieId $object */
        $object = $this->getCookieId($cookieIdString);

        self::assertSame($object->fromString($cookieIdString)->toString(), $cookieIdString);
        self::assertSame((string)$object->fromString($cookieIdString), $cookieIdString);
    }

    public function testCanSerializeToJson()
    {
        /** @var CookieId $object */
        $object = $this->getCookieId('cookie-123');
        $jsonObject = json_decode(json_encode($object));
        self::assertTrue($jsonObject == $object->toString());
    }

    /**
     *
     */
    private function getCookieId($cookieIdString)
    {
        $parameterBag = new ParameterBag();
        $parameterBag->add(['cookieId' => $cookieIdString]);
        $parameterContainer = ParameterContainer::fromParameterBag($parameterBag);

        return CookieId::fromParameterContainer($parameterContainer);
    }
}
