<?php

namespace IdentityService\View;

use IdentityService\Model\IdentityModel;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\LeadId;
use PHPUnit_Framework_TestCase;

/**
 * Class FullViewTest
 * @covers IdentityService\View\FullView
 * @uses   IdentityService\Model\IdentityModel
 * @package olando
 */
class FullViewTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = new FullView($this->getIdentity());
        $this->assertInstanceOf(FullView::class, $object);
    }

    public function testCanSerializeToJson()
    {
        $object = new FullView($this->getIdentity());
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue(isset($jsonObject->locations));
    }

    /**
     * @return IdentityModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getIdentity()
    {
        $identityId = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class);
        $leadId = $this->getMockWithoutInvokingTheOriginalConstructor(LeadId::class);

        $identityModel = $this->getMockBuilder(IdentityModel::class)
            ->setMethods(null)
            ->setConstructorArgs([
                $identityId,
                $leadId,
            ])
            ->getMock();
        /** @var IdentityModel $identityModel */
        return $identityModel;
    }
}
