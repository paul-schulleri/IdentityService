<?php
namespace IdentityService\View;

use IdentityService\Model\IdentityModel;

/**
 * Class FullView
 * @package IdentityService\View
 */
class FullView implements ViewInterface
{
    /** @var IdentityModel */
    private $identity;

    /**
     * @param $identity
     */
    public function __construct(IdentityModel $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->identity->jsonSerialize();
    }
}
