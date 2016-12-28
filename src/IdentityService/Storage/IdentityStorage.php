<?php
namespace IdentityService\Storage;

use IdentityService\Model\IdentityModel;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\IdentityStorageKey;
use Olando\Exception\CacheConnectionException;

/**
 * Class IdentityStorageService
 * @package IdentityService\Service
 */
class IdentityStorage
{
    /** @var MongoStorage */
    private $storage;

    /**
     * IdentityStorage constructor.
     * @param MongoStorage $storage
     */
    public function __construct(MongoStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param $identityId
     * @return mixed
     * @throws CacheConnectionException
     */
    public function read(IdentityId $identityId)
    {
        return $this->storage->get(
            IdentityStorageKey::fromId($identityId)
        );
    }

    /**
     * @param IdentityModel $identity
     * @throws CacheConnectionException
     */
    public function save(IdentityModel $identity)
    {
        $this->storage->set(
            (string)IdentityStorageKey::fromId(
                $identity->getId()
            ),
            $identity
        );
    }
}
