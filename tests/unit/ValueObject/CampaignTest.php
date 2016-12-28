<?php

namespace IdentityService\ValueObject;

use PHPUnit_Framework_TestCase;

/**
 * Class CampaignTest
 * @covers IdentityService\ValueObject\Campaign
 * @package olando
 */
class CampaignTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        $apiResponseMock = $this->getApiResponseMock('-value-');
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);
        $this->assertInstanceOf(Campaign::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        $apiResponseMock = $this->getApiResponseMock('-value-');
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);

        $this->assertSame($object->getAct(), '-value-');
        $this->assertSame($object->getComment(), '-value-');
        $this->assertSame($object->getControllingChannelName(), '-value-');
        $this->assertSame($object->getMarketingChannelName(), '-value-');
        $this->assertSame($object->getMarketingOfferType(), '-value-');
        $this->assertSame($object->getQuality(), '-value-');
        $this->assertSame($object->getTelephoneNumber(), '-value-');
        $this->assertSame($object->getUsageName(), '-value-');
    }

    public function testCanSerializeToJson()
    {
        $apiResponseMock = $this->getApiResponseMock('-value-');
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue($jsonObject->act == $object->getAct());
    }

    public function testCanCheckEqualitiy()
    {
        $apiResponseMock = $this->getApiResponseMock('-value-');
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);
        $objectSecond = Campaign::fromCampaignApiResponse($apiResponseMock);
        $this->assertTrue($object->equals($objectSecond));
    }

    public function testCanCheckEmptiness()
    {
        $apiResponseMock = $this->getApiResponseMock('-value-');
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);
        $this->assertFalse($object->isEmpty());

        $apiResponseMock = $this->getApiResponseMock('-value-', false);
        /** @var CampaignApiResponse $apiResponseMock */
        $object = Campaign::fromCampaignApiResponse($apiResponseMock);

        $this->assertTrue($object->isEmpty());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsException()
    {
        $apiResponseMock = $this->getApiResponseMock(null);
        /** @var CampaignApiResponse $apiResponseMock */
        Campaign::fromCampaignApiResponse($apiResponseMock);
    }

    /**
     * @param $act
     * @param bool $hasData
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getApiResponseMock($act, $hasData = true)
    {
        $mock = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiResponse::class);
        if (isset($act)) {
            $mock->method('getAct')->willReturn($act);
        }
        if ($hasData) {
            $mock->method('getQuality')->willReturn('-value-');
            $mock->method('getComment')->willReturn('-value-');
            $mock->method('getUsageName')->willReturn('-value-');
            $mock->method('getMarketingChannelName')->willReturn('-value-');
            $mock->method('getControllingChannelName')->willReturn('-value-');
            $mock->method('getTelephoneNumber')->willReturn('-value-');
            $mock->method('getMarketingOfferType')->willReturn('-value-');
        }
        return $mock;
    }
}
