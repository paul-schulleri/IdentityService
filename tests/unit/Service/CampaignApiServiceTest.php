<?php

namespace IdentityService\Service;

use GuzzleHttp\Client;
use IdentityService\Config\CampaignApiConfig;
use IdentityService\ValueObject\CampaignApiResponse;
use IdentityService\ValueObject\Locale;
use Olando\Http\ParameterContainer;
use Olando\ValueObject\AuthHeader;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CampaignTest
 * @covers IdentityService\Service\CampaignApiService
 * @uses   IdentityService\ValueObject\Campaign
 * @uses   IdentityService\ValueObject\CampaignApiResponse
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\ParameterName
 * @package olando
 */
class CampaignApiServiceTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateObject()
    {
        $this->assertInstanceOf(CampaignApiService::class, $this->createCampaignService());
    }

    public function testCanPerformGetRequest()
    {
        $campaignService = $this->createCampaignService();
        $params = ParameterContainer::fromParameterBag($this->getRequestParamBagMock());
        $response = $campaignService->getDetailsByParametersAndLocale($params, Locale::fromIsoString('de_DE'));
        $this->assertInstanceOf(CampaignApiResponse::class, $response);
    }

    /**
     * @param bool $shouldBeValidJsonResponse
     * @return PHPUnit_Framework_MockObject_MockObject
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
     * @param bool $shouldBeValidJsonResponse
     * @return CampaignApiService
     */
    private function createCampaignService($shouldBeValidJsonResponse = true)
    {
        $campaignApiConfig = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiConfig::class);
        $campaignApiConfig->method('getDefaultLanguage')->willReturn('DE');
        $campaignApiConfig->method('getDefaultRegion')->willReturn('de');
        $campaignApiConfig->method('getDefaultAct')->willReturn('157');
        $campaignApiConfig->method('getAuthHeader')->willReturn(
            $this->getMockWithoutInvokingTheOriginalConstructor(AuthHeader::class)
        );

        /** @var Client $client */
        $client = $this->getMockedClient($shouldBeValidJsonResponse);
        /** @var Serializer $serializer */
        $serializer = new Serializer(
            [new CustomNormalizer(), new GetSetMethodNormalizer()],
            [new JsonEncoder()]
        );
        /** @var CampaignApiConfig $campaignApiConfig */
        $campaign = new CampaignApiService(
            $campaignApiConfig,
            $client
        );

        return $campaign;
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

    /**
     * @return ParameterBag
     */
    private function getRequestParamBagMock()
    {
        $params = [
            'country' => 'de',
            'act' => '157',
            'ipAddress' => '172.0.0.1',
            'referrer' => 'http://example.com/referrer',
            'locale' => 'de_DE'
        ];

        return new ParameterBag($params);
    }
}
