<?php

namespace IdentityService;

use GuzzleHttp\Client;
use IdentityService\Config\CampaignApiConfig;
use IdentityService\Config\ConfigProvider;
use IdentityService\Config\MongoConfig;
use IdentityService\Controller\CreateController;
use IdentityService\Controller\ReadController;
use IdentityService\Controller\UpdateController;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use Olando\Config\ApplicationConfig;
use Olando\Config\LoggingConfig;
use Olando\ValueObject\ApiVersion;
use Olando\ValueObject\AppVersion;
use Olando\ValueObject\UrlPath;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Di
 * @covers  IdentityService\Di
 * @uses    IdentityService\Config\ConfigProvider
 * @uses    IdentityService\Controller\ReadController
 * @uses    IdentityService\Controller\CreateController
 * @uses    IdentityService\Service\IdentityService
 * @uses    IdentityService\ValueObject\IdentityId
 * @uses    IdentityService\Storage\IdentityStorage
 * @uses    IdentityService\Service\CampaignApiService
 * @uses    IdentityService\ValueObject\Uuid
 * @uses    IdentityService\Config\CampaignApiConfig
 * @uses    IdentityService\Controller\UpdateController
 * @uses    IdentityService\Storage\MongoStorage
 * @package olando
 */
class DiTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitialized()
    {
        $object = $this->getDiMock();
        $this->assertInstanceOf(Di::class, $object);
    }

    public function testCanBuildReadController()
    {
        $object = $this->getDiMock();

        $controller = $object->createReadController(IdentityId::generate());
        $this->assertInstanceOf(ReadController::class, $controller);
    }

    public function testCanCreateCreateController()
    {
        $object = $this->getDiMock();
        $this->assertInstanceOf(CreateController::class, $object->createCreateController());
    }

    public function testCanCreateUpdateController()
    {
        $object = $this->getDiMock();
        $identityId = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class);
        /** @var IdentityId $identityId */
        $this->assertInstanceOf(UpdateController::class, $object->createUpdateController($identityId));
    }

    public function testCanCreateIdentityService()
    {
        $object = $this->getDiMock();
        $this->assertInstanceOf(IdentityService::class, $object->createIdentityService());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Di
     */
    private function getDiMock()
    {
        $configProvider = $this->getMockWithoutInvokingTheOriginalConstructor(ConfigProvider::class);

        $mongoConfig = $this->getMockWithoutInvokingTheOriginalConstructor(MongoConfig::class);
        $mongoConfig->method('getHost')->willReturn('mongodb://localhost');
        $mongoConfig->method('getPort')->willReturn('27017');

        $configProvider->method('getMongoConfig')->willReturn($mongoConfig);

        $configProvider->method('getCampaignApiConfig')->willReturn(
            $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiConfig::class)
        );

        $loggingConfig = $this->getMockWithoutInvokingTheOriginalConstructor(LoggingConfig::class);
        $loggingConfig->method('getLogFile')->willReturn('unit_test.log');
        $configProvider->method('getLoggingConfig')->willReturn($loggingConfig);
        $appConfig = $this->getMockWithoutInvokingTheOriginalConstructor(ApplicationConfig::class);
        $appConfig->method('getName')->willReturn('UnitTests');
        $configProvider->method('getApplicationConfig')->willReturn($appConfig);

        $appVersion = $this->getMockWithoutInvokingTheOriginalConstructor(AppVersion::class);

        $campaignApiConfig = $this->getMockWithoutInvokingTheOriginalConstructor(CampaignApiConfig::class);
        $configProvider->method('getCampaignApiConfig')->willReturn($campaignApiConfig);

        $di = $this->getMockBuilder(Di::class)
            ->setConstructorArgs([$configProvider, $appVersion])
            ->setMethods(['createRequestClient', 'getApiVersion', 'getConfig'])
            ->getMock();

        $di->method('createRequestClient')
            ->willReturn($this->getMockedClient());

        $di->method('getConfig')
            ->willReturn($configProvider);

        $apiVersion = $this->getMockWithoutInvokingTheOriginalConstructor(ApiVersion::class);

        $apiVersion->method('setApiVersion')->willReturn(UrlPath::class);

        $di->method('getApiVersion')->willReturn($apiVersion);

        return $di;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedClient()
    {
        $mock = $this->getMockWithoutInvokingTheOriginalConstructor(Client::class);

        $response = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseInterface::class);

        $response->method('getBody')->willReturn('{}');
        $mock->method('request')->willReturn($response);

        return $mock;
    }
}
