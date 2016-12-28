<?php
namespace IdentityService\ValueObject;

/**
 * Class ParameterName
 * @package IdentityService\ValueObject
 */
class ParameterName
{
    /** @var string */
    private static $testParameter = 't';

    /** @var string */
    private static $salesforce = 'salesforce';

    /** @var string */
    private static $language = 'language';

    /** @var string */
    private static $country = 'country';

    /** @var string */
    private static $locale = 'locale';

    /** @var string */
    private static $ipAddress = 'ipAddress';

    /** @var string */
    private static $referrer = 'referrer';

    /** @var string */
    private static $targetUrl = 'targetUrl';

    /** @var string */
    private static $act = 'act';

    /** @var string */
    private static $userAgent = 'userAgent';

    /** @var string */
    private static $cookieId = 'cookieId';

    /** @var string */
    private static $leadId = 'leadId';

    /**
     * @return string
     */
    public static function getUserAgent()
    {
        return self::$userAgent;
    }

    /**
     * RequestParameterName constructor.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return string
     */
    public static function getAct()
    {
        return self::$act;
    }

    /**
     * @return string
     */
    public static function getIpAddress()
    {
        return self::$ipAddress;
    }

    /**
     * @return string
     */
    public static function getReferrer()
    {
        return self::$referrer;
    }

    /**
     * @return string
     */
    public static function getCookieId()
    {
        return self::$cookieId;
    }

    /**
     * @return string
     */
    public static function getTargetUrl()
    {
        return self::$targetUrl;
    }

    /**
     * @return string
     */
    public static function getLocale()
    {
        return self::$locale;
    }

    /**
     * @return string
     */
    public static function getLanguage()
    {
        return self::$language;
    }

    /**
     * @return string
     */
    public static function getCountry()
    {
        return self::$country;
    }

    /**
     * @return string
     */
    public static function getWebsiteTestParameter()
    {
        return self::$testParameter;
    }

    /**
     * @return string
     */
    public static function getSalesforce()
    {
        return self::$salesforce;
    }

    /**
     * @return string
     */
    public static function getLeadId()
    {
        return self::$leadId;
    }
}
