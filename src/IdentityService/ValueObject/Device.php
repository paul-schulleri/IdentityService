<?php
namespace IdentityService\ValueObject;

use Detection\MobileDetect;
use InvalidArgumentException;
use JsonSerializable;
use Olando\Http\ParameterContainer;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;

/**
 * Class Device
 * @package IdentityService\ValueObject
 */
class Device implements JsonSerializable
{
    const USER_AGENT = 'userAgent';

    /** @var string */
    private $operatingSystem;

    /** @var string */
    private $userAgentName;

    /** @var string */
    private $userAgentVersion;

    /** @var string */
    private $userAgent;

    /** @var string */
    private $isMobile;

    /**
     * @param ParameterContainer $params
     * @return Device
     * @throws InvalidArgumentException
     */
    public static function fromParameterContainer(ParameterContainer $params)
    {
        $userAgent = $params->get(self::USER_AGENT);

        $browser = new Browser($userAgent);
        $operatingSystem = new Os($userAgent);
        $mobile = new MobileDetect();

        $device = new self();
        $device->isMobile = $mobile->isMobile($userAgent) ? true : false;
        $device->userAgentName = $browser->getName();
        $device->operatingSystem = $operatingSystem->getName();
        $device->userAgentVersion = $browser->getVersion();
        $device->userAgent = $userAgent;

        return $device;
    }

    /**
     * @param Device $device
     * @return bool
     */
    public function equals(Device $device)
    {
        return $this == $device;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->userAgent ? false : true;
    }

    /**
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     * @return string
     */
    public function getUserAgentString()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getUserAgentVersion()
    {
        return $this->userAgentVersion;
    }

    /**
     * @return string
     */
    public function getIsMobile()
    {
        return $this->isMobile;
    }

    /**
     * @return string
     */
    public function getUserAgentName()
    {
        return $this->userAgentName;
    }
}
