<?php
namespace IdentityService\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use OutOfBoundsException;

/**
 * Class Location
 * @package IdentityService\ValueObject
 */
class Locale implements JsonSerializable
{
    /** @var array */
    private static $prefixMap = [
        'de_DE' => 'DEU',
        'nl_NL' => 'NLD',
        'de_CH' => 'CHE',
        'fr_CH' => 'CHF',
        'en_US' => 'USA',
        'en_IN' => 'IND',
        'hi_IN' => 'INH',
        'ja_JP' => 'JPN',
        'en_GB' => 'GBR',
        'ko_KR' => 'KOR',
        'th_TH' => 'THA',
        'en_MY' => 'MYS',
        'ms_MY' => 'MYM',
        'zh_MY' => 'MYZ',
    ];

    /** @var string */
    private $country;

    /** @var string */
    private $language;

    /**
     * Locale constructor.
     * @param $isoLocale
     * @throws InvalidArgumentException
     */
    private function __construct($isoLocale)
    {
        if (!$this->validLocale($isoLocale)) {
            throw new InvalidArgumentException('Invalid ISO locale');
        }

        $this->language = strtolower(substr($isoLocale, 0, 2));
        $this->country = strtoupper(substr($isoLocale, -2));
    }

    /**
     * @param $localeString
     * @return array
     * @throws OutOfBoundsException
     */
    public static function getPrefix($localeString)
    {
        if (!isset(self::$prefixMap[$localeString])) {
            throw new OutOfBoundsException('The requested locale is unknown');
        }


        return self::$prefixMap[$localeString];
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $string
     * @return Locale
     * @throws InvalidArgumentException
     */
    public static function fromIsoString($string)
    {
        return new self($string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param Locale $locale
     * @return bool
     */
    public function equals(Locale $locale)
    {
        return $this == $locale;
    }

    /**
     * @param $locale
     * @return bool
     */
    private function validLocale($locale)
    {
        if (strlen($locale) !== 5) {
            return false;
        }

        return !(strpos($locale, '_') !== 2);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->language . '_' . $this->country;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }
}
