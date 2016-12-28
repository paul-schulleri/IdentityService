<?php

namespace IdentityService\Controller;

use IdentityService\Di;
use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Exception\IdentityNoUpdateDataException;
use IdentityService\Model\IdentityModel;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use Olando\Http\HateoasJsonResponse;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateControllerTest
 * @package olando
 * @covers IdentityService\Controller\UpdateController
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
 */
class UpdateControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDiMock()
    {
        $di = $this->getMockBuilder(Di::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $identityId = $this->getMockBuilder(IdentityId::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityId->method('toString')
            ->willReturn('');

        $identityModel->method('getId')
            ->willReturn($identityId);

        $identityService->method('readIdentity')
            ->willReturn($identityModel);

        $identityService->method('updateIdentity')
            ->willReturn($identityModel);

        $di->method('createIdentityService')
            ->willReturn($identityService);

        /** @var Di $di */
        $controller = new UpdateController(
            IdentityId::generate(),
            $di->createIdentityService()
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
            ->method('updateIdentity')
            ->willThrowException(
                new IdentityNotFoundException('This is an unknown id')
            );

        $di->method('createIdentityService')
            ->willReturn($identityService);

        /** @var Di $di */
        $controller = new UpdateController(
            IdentityId::generate(),
            $di->createIdentityService()
        );

        $response = $controller->execute(Request::create(
            'http://example.com/identities',
            Request::METHOD_GET
        ));
        $this->assertInstanceOf(SymfonyJsonResponse::class, $response);
    }

    public function testNoUpdateDataSubmitted()
    {
        $di = $this->getDiMock();

        $identityService = $this->getMockBuilder(IdentityService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityService
            ->method('updateIdentity')
            ->willThrowException(
                new IdentityNoUpdateDataException('There was nothing to update')
            );

        $di->method('createIdentityService')
            ->willReturn($identityService);

        /** @var Di $di */
        $controller = new UpdateController(
            IdentityId::generate(),
            $di->createIdentityService()
        );

        $response = $controller->execute(Request::create(
            'http://example.com/identities',
            Request::METHOD_GET
        ));
        $this->assertInstanceOf(SymfonyJsonResponse::class, $response);
    }
}
