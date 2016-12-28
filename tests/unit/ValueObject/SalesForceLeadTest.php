<?php

namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class SalesForceLeadTest
 * @covers IdentityService\ValueObject\SalesForceLead
 * @package olando
 */
class SalesForceLeadTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = SalesForceLead::fromParameterContainer($this->getParameterContainerMock());
        $this->assertInstanceOf(SalesForceLead::class, $object);
    }

    public function testCanCreateObjectFromArray()
    {
        $object = SalesForceLead::fromArray([]);
        $this->assertInstanceOf(SalesForceLead::class, $object);
    }

    public function testCanCheckEmptiness()
    {
        $object = SalesForceLead::fromParameterContainer($this->getParameterContainerMock(true, true));
        $this->assertFalse($object->isEmpty());

        $object = SalesForceLead::fromParameterContainer($this->getParameterContainerMock(false));

        $this->assertTrue($object->isEmpty());
    }

    public function testCanCastToString()
    {
        $object = SalesForceLead::fromArray([]);
        $this->assertJson((string)$object);
        $this->assertJson($object->toString());
    }

    public function testCanSerializeToJson()
    {
        $object = SalesForceLead::fromArray([]);
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue(is_array($jsonObject));
    }

    public function testCanCheckEqualitiy()
    {
        $object = SalesForceLead::fromArray([]);
        $objectSecond = SalesForceLead::fromArray([]);
        $this->assertTrue($object->equals($objectSecond));
    }

    /**
     * @param bool $parameterBagFilled
     * @param bool $emptyValues
     * @return ParameterContainer
     */
    private function getParameterContainerMock($parameterBagFilled = true, $emptyValues = false)
    {
        $params = [];
        if ($parameterBagFilled) {
            if (!$emptyValues) {
                $params = [
                    'operatingSystem' => '-operating-system-',
                    'userAgentName' => '-user-agent-name-',
                    'userAgentString' => '-user-agent-string-',
                    'userAgentVersion' => '-user-agent-version-',
                    'isMobile' => '-is-mobile-',
                ];
            } else {
                $params = [
                    'userAgentName' => null,
                    'useragentname' => '-user-agent-name-',
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
