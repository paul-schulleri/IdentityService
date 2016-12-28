<?php
namespace IdentityService\ValueObject;

/**
 * Class IdentityStorageKey
 * @package ValueObject
 */
class IdentityStorageKey
{
    const PREFIX = 'identity_';

    /** @var IdentityId */
    private $identityId;

    /**
     * IdentityStorageKey constructor.
     * @param $identityId
     */
    private function __construct(IdentityId $identityId)
    {
        $this->identityId = $identityId;
    }

    /**
     * @param IdentityId $identityId
     * @return IdentityStorageKey
     */
    public static function fromId(IdentityId $identityId)
    {
        return new IdentityStorageKey($identityId);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return self::PREFIX . $this->identityId->toString();
    }
}
