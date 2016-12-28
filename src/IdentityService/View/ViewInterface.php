<?php
namespace IdentityService\View;

use IdentityService\Model\IdentityModel;

/**
 * Interface ViewInterface
 * @package IdentityService\View
 */
interface ViewInterface extends \JsonSerializable
{
    /**
     * ViewInterface constructor.
     * @param IdentityModel $model
     */
    public function __construct(IdentityModel $model);
}
