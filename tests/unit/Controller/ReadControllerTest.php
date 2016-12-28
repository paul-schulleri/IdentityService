<?php

namespace IdentityService\Controller;

use IdentityService\Di;
use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Model\IdentityModel;
use IdentityService\Service\IdentityService;
use IdentityService\Service\ModelConverterService;
use IdentityService\ValueObject\IdentityId;
use Olando\Http\HateoasJsonResponse;
use Olando\Logging\Logger;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ReadControllerTest
 * @package olando
 * @covers IdentityService\Controller\ReadController
 * @uses   IdentityService\Di
 * @uses   IdentityService\ValueObject\Uuid
 * @uses   IdentityService\ValueObject\IdentityId
 * @uses   IdentityService\Service\IdentityService
 * @uses   IdentityService\Service\CampaignApiService
 * @uses   IdentityService\Storage\IdentityStorage
 * @uses   olando\Config\RedisConfig
 * @uses   olando\Http\JsonResponse
 * @uses   olando\Config\ApplicationConfig
 * @uses   IdentityService\Config\CampaignApiConfig
 * @uses   IdentityService\View\FullView
 * @uses   IdentityService\View\ViewHandler
 */
class ReadControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDiMock()
    {
        $di = $this->getMockBuilder(Di::class)
            ->disableOriginalConstructor()
            ->getMock();
//
//        $controller = $this->getMockBuilder(ReadController::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $di->method('createReadodelConverterService')
//            ->willReturn($controller);

//        $modelConverterService = $this->getMockBuilder(ModelConverterService::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $di->method('createModelConverterService')
//            ->willReturn($modelConverterService);


        return $di;
    }

    public function testCanExecuteRequestWithHateoasResponse()
    {

        $di = $this->getDiMock();

        $identityService = $this->getMockBuilder(IdentityService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityModel = $this->getMockBuilder(IdentityModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityService->method('readIdentity')
            ->willReturn($identityModel);

        $di->method('createIdentityService')
            ->willReturn($identityService);

        /** @var Di $di */
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Logger $logger */
        $controller = new ReadController(
            IdentityId::generate(),
            $di->createIdentityService(),
            $logger
        );

        $response = $controller->execute(Request::create(
            'http://example.com/identities',
            Request::METHOD_GET
        ));
        $this->assertInstanceOf(HateoasJsonResponse::class, $response);
    }

    public function testIdentityNotFound()
    {
        $di = $this->getDiMock();

        $identityService = $this->getMockBuilder(IdentityService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityService
            ->method('readIdentity')
            ->will($this->throwException(
                new IdentityNotFoundException('This is an unknown id')
            ));

        $di->method('createIdentityService')
            ->willReturn($identityService);

        /** @var Di $di */
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Logger $logger */
        $controller = new ReadController(
            IdentityId::generate(),
            $di->createIdentityService(),
            $logger
        );

        $controller->execute(Request::create(
            'http://example.com/identities',
            Request::METHOD_GET
        ));

        $this->assertTrue(true);
    }
}
