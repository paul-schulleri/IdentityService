<?php
namespace IdentityService\Controller;

use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use IdentityService\View\ViewHandler;
use IdentityService\View\ViewInterface;
use Olando\Controller\ControllerInterface;
use Olando\Exception\CacheConnectionException;
use Olando\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReadController
 * @package IdentityService\Controller
 */
class ReadController implements ControllerInterface
{
    /** @var IdentityService */
    private $identityService;

    /** @var IdentityId */
    private $identityId;

    /**
     * ReadController constructor.
     * @param IdentityId $identityId
     * @param IdentityService $identityService
     */
    public function __construct(
        IdentityId $identityId,
        IdentityService $identityService
    ) {
        $this->identityId = $identityId;
        $this->identityService = $identityService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function execute(Request $request)
    {
        try {
            return JsonResponse::hateoas(
                $this->getPresentation($request)
            );
        } catch (IdentityNotFoundException $exception) {
            return JsonResponse::notFound();
        } catch (CacheConnectionException $exception) {
            return JsonResponse::unprocessableEntity();
        }
    }

    /**
     * @param Request $request
     * @return ViewInterface
     * @throws IdentityNotFoundException
     * @throws CacheConnectionException
     */
    private function getPresentation(Request $request)
    {
        return ViewHandler::fromNameAndIdentity(
            $request->get('view'),
            $this->identityService->readIdentity(
                $this->identityId
            )
        );
    }
}
