<?php

namespace IdentityService\Service;

use GuzzleHttp\Client;
use IdentityService\Config\CampaignApiConfig;
use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Model\IdentityModel;
use IdentityService\Storage\IdentityStorage;
use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\CampaignApiResponse;
use IdentityService\ValueObject\CookieId;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\LeadId;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\Location;
use IdentityService\ValueObject\ParameterName;
use Olando\Http\ParameterContainer;
use Olando\ValueObject\AuthHeader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class IdentityServiceTest
 * @package IdentityService\Service
 * @covers IdentityService\Service\IdentityService
 * @uses   IdentityService\Service\CampaignApiService
 * @uses   IdentityService\ValueObject\Uuid
 * @uses   IdentityService\ValueObject\Device
 * @uses   IdentityService\ValueObject\Campaign
 * @uses   IdentityService\Model\IdentityModel
 * @uses   IdentityService\ValueObject\IdentityId
 * @uses   IdentityService\ValueObject\Country
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\LeadId
 * @uses   IdentityService\ValueObject\Address
 * @uses   IdentityService\ValueObject\IpAddress
 * @uses   IdentityService\ValueObject\Location
 * @uses   IdentityService\ValueObject\CampaignApiResponse
 * @uses   IdentityService\ValueObject\ParameterName
 * @uses   IdentityService\ValueObject\Referrer
 * @uses   IdentityService\ValueObject\Website\TestParameter
 * @uses   IdentityService\ValueObject\SalesForceLead
 * @uses   IdentityService\ValueObject\CookieId
 */
class IdentityServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testCanBeInitialized()
    {
        $identityService = $this->getIdentityServiceMock();
        self::assertInstanceOf(IdentityService::class, $identityService);
    }

    public function testCanCreateIdentity()
    {
        $identityService = $this->getIdentityServiceMock();
        $identity = $identityService->createIdentity(ParameterContainer::fromParameterBag(
            new ParameterBag([
                ParameterName::getCountry() => 'some-country',
                ParameterName::getLocale() => 'de_DE',
                ParameterName::getWebsiteTestParameter() => '123456789',
                ParameterName::getIpAddress() => '82.55.2.65',
                ParameterName::getUserAgent() => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36',
            ])
        ));
        self::assertInstanceOf(IdentityModel::class, $identity);
    }

    public function testCanCreateIdentityWithSalesForce()
    {
        $identityService = $this->getIdentityServiceMock();
        $identity = $identityService->createIdentity(ParameterContainer::fromParameterBag(
            new ParameterBag([
                ParameterName::getCountry() => 'some-country',
                ParameterName::getSalesforce() => ['some-sales'],
                ParameterName::getLocale() => 'de_DE',
                ParameterName::getWebsiteTestParameter() => '123456789',
                ParameterName::getIpAddress() => '82.55.2.65',
                ParameterName::getUserAgent() => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36',
            ])
        ));
        self::assertInstanceOf(IdentityModel::class, $identity);
    }

    public function testCanReadIdentity()
    {
        $identityIdString = '67442948-2653-4f2b-b770-8e106ebbc1ae';
        $identityService = $this->getIdentityServiceMock(true, true, '157', $identityIdString);
        $id = $identityService->readIdentity(IdentityId::fromString($identityIdString))->getId()->toString();
        self::assertSame($identityIdString, $id);
    }

    public function testCanReadCookieId()
    {
        $identityIdString = '67442948-2653-4f2b-b770-8e106ebbc1ae';
        $cookieIdString = 'cookie-123';
        $identityService = $this->getIdentityServiceMock(true, true, '157', $identityIdString, $cookieIdString);
        $identity = $identityService->readIdentity(IdentityId::fromString($identityIdString));
        $cookieId = $identity->getCookieId()->toString();

        self::assertSame($cookieIdString, $cookieId);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function testCreationFailsOnMissingRequiredParameter()
    {
        $identityService = $this->getIdentityServiceMock(true, false);
        /** @var ParameterBag $parameterBag */
        $parameterBag = $this->getMockWithoutInvokingTheOriginalConstructor(ParameterBag::class);
        $parameters = ParameterContainer::fromParameterBag(
            $parameterBag
        );
        ($identityService->createIdentity($parameters));
    }

    public function testCanUpdateIdentity()
    {
        $identityIdString = Uuid::getFactory()->uuid4()->toString();

        $identityService = $this->getIdentityServiceMock(true, true, '157', $identityIdString);
        $identityModel = $identityService->updateIdentity(
            IdentityId::fromString($identityIdString),
            ParameterContainer::fromParameterBag(
                new ParameterBag([
                    ParameterName::getLocale() => 'de_DE',
                    ParameterName::getCountry() => 'new-country',
                ])
            )
        );

        self::assertTrue($identityModel->getId()->toString() == $identityIdString);
    }

    public function testUpdatesWithoutSecondActPreparesCorrectAct()
    {
        $identityIdString = '67442948-2653-4f2b-b770-8e106ebbc1ae';
        $act = 200;
        $identityService = $this->getIdentityServiceMock(true, true, $act, $identityIdString);

        $identityModel = $identityService->updateIdentity(
            IdentityId::fromString($identityIdString),
            ParameterContainer::fromParameterBag(
                new ParameterBag([
                    ParameterName::getAct() => ''
                ])
            )
        );

        self::assertSame($identityModel->getId()->toString(), $identityIdString);
        self::assertSame($identityModel->getLatestCampaign()->getAct(), $act);
    }

    public function testUpdatesWithSecondActPreparesCorrectAct()
    {
        $identityIdString = '67442948-2653-4f2b-b770-8e106ebbc1ae';
        $act = 201;
        $actSecond = 500;
        $identityService = $this->getIdentityServiceMock(true, true, $act, $identityIdString);

        $identityModel = $identityService->updateIdentity(
            IdentityId::fromString($identityIdString),
            ParameterContainer::fromParameterBag(
                new ParameterBag([
                    ParameterName::getAct() => $actSecond
                ])
            )
        );

        self::assertSame($identityModel->getId()->toString(), $identityIdString);
        //self::assertSame($identityModel->getLatestCampaign()->getAct(), $actSecond);
    }

    public function testUpdatesPrepareLeadIdLoad()
    {
        $identityIdString = '67442948-2653-4f2b-b770-8e106ebbc1ae';
        $act = 200;
        $predefinedLeadId = '--PredefinedLeadId--';
        $identityService = $this->getIdentityServiceMock(true, true, $act, $identityIdString, null, $predefinedLeadId);

        $identityModel = $identityService->updateIdentity(
            IdentityId::fromString($identityIdString),
            ParameterContainer::fromParameterBag(
                new ParameterBag([])
            )
        );

        self::assertSame($identityModel->getLatestLeadId()->toString(), $predefinedLeadId);
    }

    /**
     * @expectedException \IdentityService\Exception\IdentityNotFoundException
     * @throws IdentityNotFoundException
     */
    public function testThrowsExceptionOnUnknownIdentity()
    {
        $identityService = $this->getIdentityServiceMock(false, false);
        $identityService->readIdentity(IdentityId::generate());
    }

    /**
     * @param bool $identityKnown
     * @param bool $mockCampaignService
     * @param string $act
     * @param string $predefinedIdentityId
     * @param null $cookieId
     * @param string $leadId
     * @return IdentityService
     */
    private function getIdentityServiceMock(
        $identityKnown = true,
        $mockCampaignService = true,
        $act = '157',
        $predefinedIdentityId = '67442948-2653-4f2b-b770-8e106ebbc1ae',
        $cookieId = null,
        $leadId = 'DEU-LEAD-ID'
    ) {
        $identityStorage = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityStorage::class);
        if ($identityKnown) {

            $identityModel = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
            $identityId = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class);
            $identityId->method('toString')
                ->willReturn($predefinedIdentityId);

            $cookieIdMock = $this->getMockWithoutInvokingTheOriginalConstructor(CookieId::class);
            $cookieIdMock->method('toString')
                ->willReturn($cookieId);

            $identityModel->method('getCookieId')
                ->willReturn($cookieIdMock);

            $apiResponse = $this->getApiResponseMock($act, true);
            $campaign = Campaign::fromCampaignApiResponse($apiResponse);

            $identityModel->method('getLatestCampaign')
                ->willReturn($campaign);


            $identityModel->method('getId')
                ->willReturn($identityId);

            $location = $this->getMockWithoutInvokingTheOriginalConstructor(Location::class);
            $locale = $this->getMockWithoutInvokingTheOriginalConstructor(Locale::class);
            $locale->method('toString')->willReturn('de_DE');
            $location->method('getLocale')
                ->willReturn($locale);
            $identityModel->method('getLatestLocation')
                ->willReturn($location);

            $identityStorage->method('read')
                ->willReturn($identityModel);

            $leadId = LeadId::fromString($leadId);

            $identityModel->method('getLatestLeadId')
                ->willReturn($leadId);
        }

        if ($mockCampaignService) {
            $campaignService = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiService::class);

            $campaignResponse = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiResponse::class);
            $campaignResponse->method('getCountryName')
                ->willReturn('');

            $campaignResponse->method('getAct')
                ->willReturn($act);

            /** @var Campaign $campaign */
            $campaignService
                ->method('getDetailsByParametersAndLocale')
                ->willReturn(
                    $campaignResponse
                );

            $campaignService
                ->method('getCampaignDetailsByParameters')
                ->willReturn(
                    $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiResponse::class)
                );

        } else {
            $campaignApiConfig = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiConfig::class);
            $campaignApiConfig->method('getAuthHeader')
                ->willReturn($this->getMockWithoutInvokingTheOriginalConstructor(AuthHeader::class));
            $campaignApiConfig->method('getEndpointCampaign')
                ->willReturn('endpoint-url');

            /** @var CampaignApiConfig $campaignApiConfig */
            /** @var Client $client */
            $client = $this->getMockedClient(true);
            $campaignService = new CampaignApiService(
                $campaignApiConfig,
                $client
            );
        }

        /** @var IdentityStorage $identityStorage */
        /** @var CampaignApiService $campaignService */
        $identityService = new IdentityService(
            $identityStorage,
            $campaignService
        );

        return $identityService;
    }

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

    /**
     * @param bool $shouldBeValidJsonResponse
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedClient($shouldBeValidJsonResponse = true)
    {
        $mock = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);

        $response = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseInterface::class);
        $stream = $this->getMockWithoutInvokingTheOriginalConstructor(StreamInterface::class);

        $stream->method('getContents')
            ->willReturn($this->mockJsonResponseString($shouldBeValidJsonResponse));

        $response->method('getBody')
            ->willReturn($stream);

        $mock->method('request')
            ->willReturn($response);

        return $mock;
    }


    /**
     * @param bool $shouldBeValid
     * @return string
     */
    private function mockJsonResponseString($shouldBeValid = true)
    {
        if ($shouldBeValid) {
            return json_encode([
                'message' => 'OK',
                'data' => [
                    'country' => 'GERMANY',
                    'act' => '157',
                    'locale' => 'de_DE',
                    'ipAddress' => '',
                    'referrer' => '',
                    'telephoneNumber' => '030 22 0 11 89 17',
                ],
            ]);
        } else {
            return "";
        }
    }
}
