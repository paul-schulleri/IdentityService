<?php

namespace IdentityService;

use GuzzleHttp\Client;
use IdentityService\Config\MongoConfig;
use IdentityService\Controller\CreateController;
use IdentityService\Controller\ReadController;
use IdentityService\Controller\UpdateController;
use IdentityService\Service\CampaignApiService;
use IdentityService\Service\IdentityService;
use IdentityService\Storage\IdentityStorage;
use IdentityService\Storage\MongoStorage;
use IdentityService\ValueObject\IdentityId;
use MongoDB\Client as MongoClient;
use MongoDB\Exception\InvalidArgumentException;
use Olando\Di\DiAbstract;

/**
 * Class Di
 * @package olando
 */
class Di extends DiAbstract
{
    /** @var MongoStorage */
    private $mongo;

    /**
     * @param IdentityId $identityId
     * @return ReadController
     * @throws InvalidArgumentException
     */
    public function createReadController(IdentityId $identityId)
    {
        return new ReadController(
            $identityId,
            $this->createIdentityService(),
            $this->getLogger()
        );
    }

    /**
     * @return CreateController
     * @throws InvalidArgumentException
     */
    public function createCreateController()
    {
        return new CreateController($this->createIdentityService());
    }

    /**
     * @param IdentityId $identityId
     * @return UpdateController
     * @throws InvalidArgumentException
     */
    public function createUpdateController(IdentityId $identityId)
    {
        return new UpdateController($identityId, $this->createIdentityService());
    }

    /**
     * @return IdentityService
     * @throws InvalidArgumentException
     */
    public function createIdentityService()
    {
        return new IdentityService(
            $this->createIdentityStorageService(),
            $this->createCampaignService()
        );
    }

    /**
     * @return IdentityStorage
     * @throws InvalidArgumentException
     */
    private function createIdentityStorageService()
    {
        return new IdentityStorage(
            $this->getMongoStorage()
        );
    }


    /**
     * @return CampaignApiService
     */
    public function createCampaignService()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $campaignApiConfig = $this->getConfig()->getCampaignApiConfig();

        return new CampaignApiService(
            $campaignApiConfig,
            new Client()
        );
    }


    /**
     * @return MongoStorage
     * @throws InvalidArgumentException
     */
    protected function getMongoStorage()
    {
        /** @var MongoConfig $config */
        $config = $this->getConfig()->getMongoConfig();

        if ($this->mongo === null) {
            $this->mongo = new MongoStorage(
                new MongoClient($config->getHost() . ':' . $config->getPort()),
                $config,
                $this->getAppVersion(),
                $this->getApiVersion()
            );
        }

        return $this->mongo;
    }
}
