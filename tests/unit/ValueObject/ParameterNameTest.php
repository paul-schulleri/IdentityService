<?php
namespace IdentityService\ValueObject;

/**
 * Class ParameterNameTest
 * @package IdentityService\ValueObject
 * @covers IdentityService\ValueObject\ParameterName
 */
class ParameterNameTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodsReturningExpectedValues()
    {
        self::assertSame('userAgent', ParameterName::getUserAgent());
        self::assertSame('referrer', ParameterName::getReferrer());
        self::assertSame('ipAddress', ParameterName::getIpAddress());
        self::assertSame('locale', ParameterName::getLocale());
        self::assertSame('language', ParameterName::getLanguage());
        self::assertSame('act', ParameterName::getAct());
        self::assertSame('country', ParameterName::getCountry());
        self::assertSame('targetUrl', ParameterName::getTargetUrl());
        self::assertSame('t', ParameterName::getWebsiteTestParameter());
        self::assertSame('salesforce', ParameterName::getSalesforce());
        self::assertSame('cookieId', ParameterName::getCookieId());
        self::assertSame('leadId', ParameterName::getLeadId());
    }
}
