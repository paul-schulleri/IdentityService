<?php
namespace IdentityService\View;

use IdentityService\Model\IdentityModel;

/**
 * Class ViewHandler
 * @package IdentityService\View
 */
class ViewHandler
{
    /**
     * @param string $viewName
     * @param IdentityModel $identityModel
     * @return ViewInterface
     */
    public static function fromNameAndIdentity($viewName, IdentityModel $identityModel)
    {
        if ($viewName === 'website') {
            return new WebsiteView($identityModel);
        }

        return new FullView($identityModel);
    }
}
