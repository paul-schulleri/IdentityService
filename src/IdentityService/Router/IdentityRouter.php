<?php
namespace IdentityService\Router;

use IdentityService\Di;
use IdentityService\Exception\InvalidIdentityIdException;
use IdentityService\ValueObject\IdentityId;
use MongoDB\Exception\InvalidArgumentException;
use OutOfBoundsException;
use Olando\Di\DiInterface;
use Olando\Router\RouterInterface;
use Olando\ValueObject\RequestUri;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IdentityRouter
 * @package IdentityService\Router
 */
class IdentityRouter implements RouterInterface
{
    /** @var Di */
    private $di;

    /**
     * Router constructor.
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param Request $request
     * @return mixed|null
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws InvalidIdentityIdException
     */
    public function route(Request $request)
    {
        $requestUri = RequestUri::fromString($request->getRequestUri());
        $urlPath = $requestUri->getPath();
        $this->di->setApiVersion($urlPath->getApiVersion());

        if ($urlPath->getFirstSegment() !== 'identities') {
            return null;
        }

        if ($urlPath->hasSecondSegment()) {
            return $this->handleAccessToIdentity(
                $request->getMethod(), $urlPath->getSecondSegment()
            );
        }

        return $this->handleCreateIdentity($request->getMethod());
    }

    /**
     * @param $method
     * @param $id
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidIdentityIdException
     */
    private function handleAccessToIdentity($method, $id)
    {
        try {
            $identityId = IdentityId::fromString($id);
        } catch (InvalidIdentityIdException $e) {
            return null;
        }

        if ($method === Request::METHOD_GET) {
            return $this->di->createReadController($identityId);
        } elseif ($method === Request::METHOD_PATCH || $method === Request::METHOD_POST) {
            return $this->di->createUpdateController($identityId);
        }

        return $this->di->createMethodNotAllowedController();
    }

    /**
     * @param $method
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function handleCreateIdentity($method)
    {
        if ($method === Request::METHOD_POST) {
            return $this->di->createCreateController();
        }

        return $this->di->createMethodNotAllowedController();
    }
}
