<?php

namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CampaignApiResponseTest
 * @covers IdentityService\ValueObject\CampaignApiResponse
 * @package olando
 */
class CampaignApiResponseTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = CampaignApiResponse::fromParameterContainer($parameterContainer);
        self::assertInstanceOf(CampaignApiResponse::class, $object);
    }

    public function testCanGetPropertyValues()
    {
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = CampaignApiResponse::fromParameterContainer($parameterContainer);

        self::assertSame($object->getTelephoneNumber(), '-telephone-number-');
        self::assertSame($object->getRegionName(), '-region-name-');
        self::assertSame($object->getAct(), '-act-');
        self::assertSame($object->getGeoZipCode(), '-geo-zip-code-');
        self::assertSame($object->getGeoCity(), '-geo-city-');
        self::assertSame($object->getGeoRegion(), '-geo-region-');
        self::assertSame($object->getComment(), '-comment-');
        self::assertSame($object->getCountry(), '-country-');
        self::assertSame($object->getQuality(), '-quality-');
        self::assertSame($object->getUsageName(), '-usage-name-');
        self::assertSame($object->getMarketingChannelName(), '-marketing-channel-name-');
        self::assertSame($object->getControllingChannelName(), '-controlling-channel-name-');
        self::assertSame($object->getMarketingOfferType(), '-marketing-offer-type-');
    }

    public function testCanHandleFallbackLowerCaseParameter()
    {
        $parameterContainer = $this->getParameterContainerMock(true, true);
        /** @var ParameterContainer $parameterContainer */
        $object = CampaignApiResponse::fromParameterContainer($parameterContainer);

        self::assertSame($object->getTelephoneNumber(), '-telephone-number-');
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
                    'telephoneNumber' => '-telephone-number-',
                    'regionName' => '-region-name-',
                    'act' => '-act-',
                    'geoZipCode' => '-geo-zip-code-',
                    'geoRegion' => '-geo-region-',
                    'geoCity' => '-geo-city-',
                    'comment' => '-comment-',
                    'country' => '-country-',
                    'quality' => '-quality-',
                    'usageName' => '-usage-name-',
                    'marketingChannelName' => '-marketing-channel-name-',
                    'controllingChannelName' => '-controlling-channel-name-',
                    'marketingOfferType' => '-marketing-offer-type-',
                ];
            } else {
                $params = [
                    'telephoneNumber' => null,
                    'telephonenumber' => '-telephone-number-',
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
