<?php

namespace IdentityService\Storage;

use IdentityService\Config\MongoConfig;
use MongoDB\Client;
use Olando\ValueObject\ApiVersion;
use Olando\ValueObject\AppVersion;

/**
 * Class MongoStorageTest
 * @package IdentityService\Storage
 */
class MongoStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCanSetValue()
    {
        $storage = $this->mock();

        $key = 'redis-key';
        $value = 'redis-value';
        $mongoStorage = $this->mock();
        $mongoStorage->set($key, $value);

        self::assertTrue($mongoStorage->has($key));
        self::assertSame($value, $mongoStorage->get($key));
        $mongoStorage->delete($key);
    }

    /**
     * @return MongoStorage
     */
    private function mock()
    {
        $client = new Client('mongodb://localhost:27017');
        $config = new MongoConfig('mongodb://localhost', 27017, 1);
        $appVersion = $this->getMockWithoutInvokingTheOriginalConstructor(AppVersion::class);
        $apiVersion = $this->getMockWithoutInvokingTheOriginalConstructor(ApiVersion::class);

        return new MongoStorage(
            $client,
            $config,
            $appVersion,
            $apiVersion
        );
    }
}
