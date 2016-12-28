<?php

namespace IdentityService\Controller;

use Exception;
use IdentityService\Service\IdentityService;
use Olando\Controller\ControllerInterface;
use Olando\Http\HateoasJsonResponse;
use Olando\Http\ParameterContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 * @package IdentityService\Controller
 */
class CreateController implements ControllerInterface
{
    /** @var IdentityService */
    private $identityService;

    /**
     * CreateController constructor.
     * @param $identityService
     */
    public function __construct(IdentityService $identityService)
    {
        $this->identityService = $identityService;
    }

    /**
     * @param Request $request
     * @return HateoasJsonResponse
     * @throws Exception
     */
    public function execute(Request $request)
    {
        $identityModel = $this->identityService->createIdentity(
            ParameterContainer::fromParameterBag(
                $request->request
            )
        );

        $response = new HateoasJsonResponse(
            ['uuid' => $identityModel->getId()->toString()],
            Response::HTTP_CREATED
        );

        $response->addLink(
            'self', 'identities' . DIRECTORY_SEPARATOR . $identityModel->getId()
        );

        return $response;
    }
}
