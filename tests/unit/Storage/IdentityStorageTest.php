<?php

namespace IdentityService\Storage;

use IdentityService\Model\IdentityModel;
use IdentityService\ValueObject\IdentityId;
use Olando\Storage\RedisStorage;

/**
 * Class IdentityStorageTest
 * @package IdentityService\Storage
 * @covers IdentityService\Storage\IdentityStorage
 * @uses   IdentityService\ValueObject\IdentityStorageKey
 */
class IdentityStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitialized()
    {
        $object = new IdentityStorage($this->getMongoStorageMock());
        $this->assertInstanceOf(IdentityStorage::class, $object);
    }

    public function testCanWriteToStorage()
    {
        $object = new IdentityStorage($this->getMongoStorageMock());
        $model = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
        $model->method('getId')->willReturn(
            $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class)
        );
        $object->save($model);
        $this->assertTrue(true);
    }

    public function testCanReadFromStorage()
    {
        $object = new IdentityStorage($this->getMongoStorageMock());
        $object->read(
            $this->getMockWithoutInvokingTheOriginalConstructor(IdentityId::class)
        );
        $this->assertTrue(true);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RedisStorage
     */
    private function getMongoStorageMock()
    {
        $mongoStorage = $this->getMockWithoutInvokingTheOriginalConstructor(MongoStorage::class);
        $model = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
        $mongoStorage->method('get')->willReturn($model);

        return $mongoStorage;
    }
}

