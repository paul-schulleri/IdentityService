<?php

namespace IdentityService\ValueObject;

use Olando\Http\ParameterContainer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class DeviceTest
 * @covers IdentityService\ValueObject\Device
 * @package olando
 */
class DeviceTest extends PHPUnit_Framework_TestCase
{
    public $userAgent;

    public function setUserAgentToDesktopChromeMac()
    {
        $this->userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36';
    }

    public function setUserAgentToDesktopFirefoxWin()
    {
        $this->userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1';
    }

    public function setUserAgentToDesktopInternetExplorerWin()
    {
        $this->userAgent = 'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko';
    }

    public function setUserAgentToMobile()
    {
        $this->userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3';
    }

    public function testOperatingSystemUndefined()
    {
        $this->userAgent = '';
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getOperatingSystem(), 'unknown');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
    }

    public function testOperatingSystemWin()
    {
        $this->setUserAgentToDesktopFirefoxWin();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getOperatingSystem(), 'Windows');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
    }

    public function testUserAgentFirefox()
    {
        $this->setUserAgentToDesktopFirefoxWin();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getUserAgentName(), 'Firefox');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
    }

    public function testUserAgentInternetExplorer()
    {
        $this->setUserAgentToDesktopInternetExplorerWin();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getUserAgentName(), 'Internet Explorer');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
    }

    public function testIsMobileIfIphone()
    {
        $this->setUserAgentToMobile();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getIsMobile(), true);
        $this->assertSame($object->getUserAgentName(), 'Safari');
        $this->assertSame($object->getOperatingSystem(), 'iOS');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
        $this->assertSame($object->getUserAgentVersion(), '5.1');
    }

    public function testCanCreateObject()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertInstanceOf(Device::class, $object);
    }

    public function testCanGetUserAgent()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getUserAgent(), $this->userAgent);
    }

    public function testCanGetPropertyValues()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getIsMobile(), false);
        $this->assertSame($object->getUserAgentName(), 'Chrome');
        $this->assertSame($object->getOperatingSystem(), 'OS X');
        $this->assertSame($object->getUserAgentString(), $this->userAgent);
        $this->assertSame($object->getUserAgentVersion(), '47.0.2526.111');
    }

    public function testCanHandleFallbackLowerCaseParameter()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock(true);
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertSame($object->getUserAgentName(), 'Chrome');
    }

    public function testCanCheckEqualitiy()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock(true);
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $objectSecond = Device::fromParameterContainer($parameterContainer);
        $this->assertTrue($object->equals($objectSecond));
    }

    public function testCanCheckEmptiness()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock(true);
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertFalse($object->isEmpty());

        $parameterContainer = $this->getParameterContainerMock(false);
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $this->assertTrue($object->isEmpty());
    }

    public function testCanSerializeToJson()
    {
        $this->setUserAgentToDesktopChromeMac();
        $parameterContainer = $this->getParameterContainerMock();
        /** @var ParameterContainer $parameterContainer */
        $object = Device::fromParameterContainer($parameterContainer);
        $jsonObject = json_decode(json_encode($object));
        $this->assertTrue($jsonObject->userAgentName === $object->getUserAgentName());
    }

    /**
     * @param bool $parameterBagFilled
     * @return ParameterContainer
     */
    private function getParameterContainerMock($parameterBagFilled = true)
    {
        $params = [];

        if ($parameterBagFilled) {
            $params = ['userAgent' => $this->userAgent];
        }

        $parameterBag = $this->getMockBuilder(ParameterBag::class)
            ->setConstructorArgs([$params])
            ->setMethods(null)
            ->getMock();

        /** @var ParameterBag $parameterBag */
        return ParameterContainer::fromParameterBag($parameterBag);
    }
}
