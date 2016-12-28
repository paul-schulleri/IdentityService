<?php

namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ReferrerTest
 * @covers IdentityService\ValueObject\Referrer
 * @uses   IdentityService\ValueObject\ParameterName
 * @package olando
 */
class ReferrerTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);
        self::assertInstanceOf(Referrer::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);
//        self::assertSame('-referrer-', (string)$object);
        self::assertSame('-referrer-', (string)$object->getReferrerUrl());
        self::assertSame('-target-url-', (string)$object->getTargetUrl());
    }

    public function testCanCheckEquality()
    {
        $parameterContainer = $this->getParameterContainerMock(true, true);
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);
        $objectSecond = Referrer::fromParameterContainer($parameterContainer);
        self::assertTrue($object->equals($objectSecond));
    }

    public function testCanCheckEmptiness()
    {
        $parameterContainer = $this->getParameterContainerMock(true, false);
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);
        self::assertFalse($object->isEmpty());

        $parameterContainer = $this->getParameterContainerMock(false);
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);

        self::assertTrue($object->isEmpty());
    }

    public function testCanSerializeToJson()
    {
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Referrer::fromParameterContainer($parameterContainer);
        $jsonObject = json_decode(json_encode($object));

        self::assertSame((array)$jsonObject, $object->jsonSerialize());
    }

    /**
     * @param bool $parameterBagFilled
     * @param bool $sameReferrer
     * @return ParameterContainer
     */
    private function getParameterContainerMock($parameterBagFilled = true, $sameReferrer = false)
    {
        $params = [];
        if ($parameterBagFilled) {
            if (!$sameReferrer) {
                $params = [
                    'referrer' => '-referrer-',
                    'targetUrl' => '-target-url-',
                ];
            } else {
                $params = [
                    'referrer' => '-same-url-',
                    'targetUrl' => '-same-url-',
                ];
            }
        }

        $parameterBag = $this->getMockBuilder(ParameterBag::class)
            ->setConstructorArgs([$params])
            ->setMethods(null)
            ->getMock();

        /** @var ParameterBag $parameterBag */
        return ParameterContainer::fromParameterBag($parameterBag);
    }
}
