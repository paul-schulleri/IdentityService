<?php
namespace IdentityService\Controller;

use Exception;
use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Exception\IdentityNoUpdateDataException;
use IdentityService\Exception\InvalidUuidException;
use IdentityService\Model\IdentityModel;
use IdentityService\Service\IdentityService;
use IdentityService\ValueObject\IdentityId;
use InvalidArgumentException;
use Olando\Controller\ControllerInterface;
use Olando\Exception\CacheConnectionException;
use Olando\Exception\MissingRequiredParameterException;
use Olando\Http\HateoasJsonResponse;
use Olando\Http\JsonResponse;
use Olando\Http\ParameterContainer;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 * @package IdentityService\Controller
 */
class UpdateController implements ControllerInterface
{
    /** @var IdentityService */
    private $identityService;

    /** @var IdentityId */
    private $identityId;

    /**
     * UpdateController constructor.
     * @param IdentityId $identityId
     * @param IdentityService $identityService
     */
    public function __construct(IdentityId $identityId, IdentityService $identityService)
    {
        $this->identityService = $identityService;
        $this->identityId = $identityId;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function execute(Request $request)
    {
        try {
            return new HateoasJsonResponse(
                ['uuid' => $this->getIdentityModel($request)->getId()->toString()],
                Response::HTTP_ACCEPTED
            );
        } catch (IdentityNotFoundException $exception) {
            return JsonResponse::notFound();
        } catch (IdentityNoUpdateDataException $exception) {
            return JsonResponse::notModified();
        } catch (Exception $exception) {
            return JsonResponse::unprocessableEntity();
        }
    }

    /**
     * @param Request $request
     * @return IdentityModel
     * @throws IdentityNotFoundException
     * @throws InvalidUuidException
     * @throws RuntimeException
     * @throws CacheConnectionException
     * @throws InvalidArgumentException
     * @throws MissingRequiredParameterException
     */
    private function getIdentityModel(Request $request)
    {
        return $this->identityService->updateIdentity(
            $this->identityId,
            ParameterContainer::fromParameterBag(
                $request->request
            )
        );
    }
}
