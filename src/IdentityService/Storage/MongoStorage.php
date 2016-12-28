<?php
namespace IdentityService\Storage;

use IdentityService\Config\MongoConfig;
use IdentityService\ValueObject\IdentityStorageKey;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use Olando\Exception\CacheConnectionException;
use Olando\ValueObject\ApiVersion;
use Olando\ValueObject\AppVersion;

/**
 * Class MongoStorage
 * @package IdentityService\Storage
 */
class MongoStorage
{
    const DATABASE_NAME = 'identityService';
    const COLLECTION_NAME = 'identities';

    /** @var Client */
    private $clientClass;

    /** @var Collection */
    private $connection;

    /**
     * MongoStorage constructor.
     * @param Client $clientClass
     * @param MongoConfig $mongoConfig
     * @param AppVersion|null $appVersion
     * @param ApiVersion|null $apiVersion
     */
    public function __construct(
        Client $clientClass,
        MongoConfig $mongoConfig,
        AppVersion $appVersion = null,
        ApiVersion $apiVersion = null
    ) {
        $this->clientClass = $clientClass;
    }

    /**
     * @param $key
     * @return int
     * @throws CacheConnectionException
     */
    public function has($key)
    {
        return count(
            $this->getMongoConnection()->findOne(['id' => $key])
        );
    }

    /**
     * @return Collection
     */
    private function getMongoConnection()
    {
        if ($this->connection === null) {
            $database = $this->clientClass->selectDatabase(
                self::DATABASE_NAME
            );
            $this->connection = $database->selectCollection(
                self::COLLECTION_NAME
            );
        }

        return $this->connection;
    }

    /**
     * @param $key
     * @return mixed
     * @throws CacheConnectionException
     */
    public function get(IdentityStorageKey $key)
    {
        $cursor = $this->getMongoConnection()->findOne(
            ['id' => $key->toString()]
        );

        return unserialize(json_decode($cursor['data']));
    }

    /**
     * @param $key
     * @param $value
     * @throws CacheConnectionException
     * @return InsertOneResult|UpdateResult
     */
    public function set($key, $value)
    {
        if ($this->has($key)) {
            return $this->update($key, $value);
        }

        return $this->insert($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return UpdateResult
     */
    private function update($key, $value)
    {
        return $this->getMongoConnection()->updateOne(
            ['id' => $key],
            ['$set' => ['data' => json_encode(serialize($value))]]
        );
    }

    /**
     * @param $key
     * @param $value
     * @return InsertOneResult
     */
    private function insert($key, $value)
    {
        return $this->getMongoConnection()->insertOne([
            'id' => $key,
            'data' => json_encode(serialize($value))
        ]);
    }
}
