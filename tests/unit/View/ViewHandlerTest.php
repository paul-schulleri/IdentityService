<?php

namespace IdentityService\View;

use IdentityService\Model\IdentityModel;
use PHPUnit_Framework_TestCase;

/**
 * Class ViewHandlerTest
 * @covers IdentityService\View\ViewHandler
 * @uses   IdentityService\View\WebsiteView
 * @uses   IdentityService\View\FullView
 * @package olando
 */
class ViewHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateObject()
    {
        $object = new ViewHandler();
        $this->assertInstanceOf(ViewHandler::class, $object);

        $identityModel = $this->getMockWithoutInvokingTheOriginalConstructor(IdentityModel::class);
        /** @var IdentityModel $identityModel */
        $object = ViewHandler::fromNameAndIdentity('website', $identityModel);
        $this->assertInstanceOf(ViewInterface::class, $object);

        $object = ViewHandler::fromNameAndIdentity('-any-other-view-name-', $identityModel);
        $this->assertInstanceOf(ViewInterface::class, $object);

    }
}
