<?php

namespace IdentityService\Router;

use IdentityService\Config\CampaignApiConfig;
use IdentityService\Config\ConfigProvider;
use IdentityService\Config\MongoConfig;
use IdentityService\Controller\CreateController;
use IdentityService\Controller\ReadController;
use IdentityService\Controller\UpdateController;
use IdentityService\Di;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use Olando\Config\ApplicationConfig;
use Olando\Config\LoggingConfig;
use Olando\Controller\MethodNotAllowedController;
use Olando\Di\DiInterface;
use Olando\Logging\Logger;
use Olando\ValueObject\ApiVersion;
use Olando\ValueObject\AppVersion;
use Olando\ValueObject\UrlPath;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouterTest
 * @covers  IdentityService\Router\IdentityRouter
 * @uses    IdentityService\ValueObject\Uuid
 * @uses    IdentityService\ValueObject\IdentityId
 * @uses    IdentityService\Controller\CreateController
 * @uses    IdentityService\Controller\ReadController
 * @uses    IdentityService\Controller\UpdateController
 * @uses    IdentityService\Service\IdentityService
 * @uses    IdentityService\Storage\IdentityStorage
 * @uses    IdentityService\Di
 * @uses    IdentityService\Service\CampaignApiService
 * @uses    IdentityService\Storage\MongoStorage
 * @package olando
 */
class IdentityRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function provideRoutableRequests()
    {
        return [
            [
                Request::create(
                    'http://example.com/identities',
                    Request::METHOD_POST
                ),
                CreateController::class,
                'createCreateController'
            ],
            [
                Request::create(
                    'http://example.com/identities',
                    Request::METHOD_GET
                ),
                MethodNotAllowedController::class,
                'createMethodNotAllowedController'
            ],
            [
                Request::create(
                    'http://example.com/identities/b33b8ab6-844d-4c0d-811c-5f70e01333a8',
                    Request::METHOD_PUT
                ),
                MethodNotAllowedController::class,
                'createMethodNotAllowedController'
            ],
            [
                Request::create(
                    'http://example.com/identities/b33b8ab6-844d-4c0d-811c-5f70e01333a8',
                    Request::METHOD_POST
                ),
                UpdateController::class,
                'createUpdateController'
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideNonRoutableRequests()
    {
        return [
            [
                Request::create('http://example.com/identities/no-uuid', Request::METHOD_GET)
            ],
            [
                Request::create('http://example.com/is-not-identities', Request::METHOD_GET)
            ],
        ];
    }

    /**
     * @dataProvider provideRoutableRequests
     * @param Request $request
     * @param $controllerClass
     * @param $controllerCreateMethod
     */
    public function testRoutesRequests(Request $request, $controllerClass, $controllerCreateMethod)
    {
        $identityId = $this->getMockBuilder(IdentityId::class)
            ->disableOriginalConstructor()
            ->getMock();

        $di = $this->getDiMock();
        $method = $di->method($controllerCreateMethod);
        /** @var Di $di */
        if ($controllerClass == UpdateController::class) {
            $createIdentityService = $di->createIdentityService();
            $method->willReturn(new $controllerClass($identityId, $createIdentityService));
        } else {
            $createIdentityService = $di->createIdentityService();
            $method->willReturn(new $controllerClass($createIdentityService));
        }

        $router = new IdentityRouter($di);
        $controller = $router->route($request);

        $this->assertInstanceOf($controllerClass, $controller, 'Method: ' . $controllerCreateMethod);
    }

    /**
     * @dataProvider provideNonRoutableRequests
     * @param Request $request
     */
    public function testDoesNotRouteRequests(Request $request)
    {
        $di = $this->getDiMock();
        /** @var DiInterface $di */
        $router = new IdentityRouter($di);
        $this->assertNull($router->route($request));
    }

    public function testDoesRouteToReadController()
    {
        $request = Request::create(
            'http://example.com/identities/b33b8ab6-844d-4c0d-811c-5f70e01333a8',
            Request::METHOD_GET
        );
        $di = $this->getMockBuilder(Di::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityService = $this->getMockBuilder(IdentityService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $di->method('createIdentityService')
            ->willReturn($identityService);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Logger $logger */
        $method = $di->method('createReadController');
        /** @var Di $di */
        $method->willReturn(new ReadController(
            IdentityId::generate(),
            $di->createIdentityService(),
            $logger
        ));

        $router = new IdentityRouter($di);
        $controller = $router->route($request);
        $this->assertInstanceOf(ReadController::class, $controller);
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
