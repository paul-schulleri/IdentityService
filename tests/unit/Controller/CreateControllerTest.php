<?php
namespace IdentityService\Controller;

use IdentityService\Di;
use IdentityService\Model\IdentityModel;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use Olando\Http\HateoasJsonResponse;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateControllerTest
 * @package olando
 * @covers IdentityService\Controller\CreateController
 * @uses   IdentityService\Di
 * @uses   IdentityService\ValueObject\Uuid
 * @uses   IdentityService\Service\IdentityService
 * @uses   IdentityService\Service\CampaignApiService
 * @uses   IdentityService\Storage\IdentityStorage
 * @uses   olando\Config\RedisConfig
 * @uses   olando\Config\ApplicationConfig
 * @uses   IdentityService\Config\CampaignApiConfig
 */
class CreateControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $uuid
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDiMock($uuid)
    {
        $di = $this->getMockBuilder(Di::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityService = $this->getMockBuilder(IdentityService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identityModel = $this->getMockBuilder(IdentityModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $identityModel->method('getId')->willReturn($uuid);

        $identityService->method('createIdentity')
            ->willReturn($identityModel);

        $di->method('createIdentityService')
            ->willReturn($identityService);

        return $di;
    }

    public function testCanExecuteRequestWithHateoasResponse()
    {

        /** @var Di $di */
        $identityService = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityService::class);

        $identity = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
        $identity->method('getId')
            ->willReturn($this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class));

        $identityService->method('createIdentity')
            ->willReturn($identity);

        /** @var IdentityService $identityService */
        $controller = new CreateController($identityService);
        $response = $controller->execute(Request::create(
            'http://example.com/identities',
            Request::METHOD_GET,
            ['country' => 'de']
        ));
        $this->assertInstanceOf(HateoasJsonResponse::class, $response);
    }
}
