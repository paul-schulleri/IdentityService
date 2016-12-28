<?php
namespace IdentityService\Service;

use Exception;
use IdentityService\Exception\IdentityNotFoundException;
use IdentityService\Exception\InvalidUuidException;
use IdentityService\Model\IdentityModel;
use IdentityService\Storage\IdentityStorage;
use IdentityService\ValueObject\Address;
use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\CampaignApiResponse;
use IdentityService\ValueObject\CookieId;
use IdentityService\ValueObject\Country;
use IdentityService\ValueObject\Device;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\IpAddress;
use IdentityService\ValueObject\LeadId;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\Location;
use IdentityService\ValueObject\ParameterName;
use IdentityService\ValueObject\Referrer;
use IdentityService\ValueObject\SalesForceLead;
use IdentityService\ValueObject\Website\TestParameter;
use InvalidArgumentException;
use Olando\Exception\CacheConnectionException;
use Olando\Exception\MissingRequiredParameterException;
use Olando\Helper\ValidatorTrait;
use Olando\Http\JsonResponse;
use Olando\Http\ParameterContainer;
use RuntimeException;

/**
 * Class IdentityService
 * @package IdentityService\Service
 */
class IdentityService
{
    use ValidatorTrait;

    const SALESFORCE = 'salesforce';

    /** @var IdentityStorage */
    protected $storage;

    /** @var IdentityStorage */
    protected $campaignService;

    /**
     * IdentityService constructor.
     * @param IdentityStorage $identityStorage
     * @param CampaignApiService $campaignService
     */
    public function __construct(
        IdentityStorage $identityStorage,
        CampaignApiService $campaignService
    ) {
        $this->storage = $identityStorage;
        $this->campaignService = $campaignService;
    }

    /**
     * @param IdentityId $identityId
     * @return IdentityModel
     * @throws CacheConnectionException
     * @throws IdentityNotFoundException
     */
    public function readIdentity(IdentityId $identityId)
    {
        /** @var IdentityModel $identityModel */
        $identityModel = $this->storage->read($identityId);

        if (!$identityModel instanceof IdentityModel) {
            throw new IdentityNotFoundException('Not valid object for ' . $identityId);
        }

        return $identityModel;
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @return IdentityModel
     * @throws InvalidUuidException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws CacheConnectionException
     * @throws MissingRequiredParameterException
     */
    public function createIdentity(ParameterContainer $parameterContainer)
    {
        $identity = $this->prepareIdentityByParameters($parameterContainer);
        $this->storage->save($identity);

        return $identity;
    }

    /**
     * @param IdentityId $identityId
     * @param ParameterContainer $parameterContainer
     * @return IdentityModel
     * @throws InvalidUuidException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws CacheConnectionException
     * @throws MissingRequiredParameterException
     * @throws IdentityNotFoundException
     */
    public function updateIdentity(IdentityId $identityId, ParameterContainer $parameterContainer)
    {
        $existingIdentity = $this->readIdentity($identityId);
        $act = $this->prepareAct(
            $existingIdentity, $parameterContainer
        );

        $parameterContainer->add(ParameterName::getAct(), $act);
        $incomingIdentity = $this->prepareIdentityByParameters(
            $parameterContainer, $existingIdentity
        );

        $existingIdentity->mergeIdentity($incomingIdentity);
        $this->storage->save($existingIdentity);

        return $existingIdentity;
    }

    /**
     * @param IdentityModel $identity
     * @param ParameterContainer $parameterContainer
     * @return string
     */
    private function prepareAct(IdentityModel $identity, ParameterContainer $parameterContainer)
    {
        $campaign = $identity->getLatestCampaign();

        if ($this->invalidCampaign($parameterContainer, $campaign)) {
            return (string)$identity->getLatestCampaign()->getAct();
        }

        return (string)$parameterContainer->get(ParameterName::getAct());
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param $existingIdentity
     * @param Locale $locale
     * @return LeadId|null
     * @throws InvalidUuidException
     */
    private function prepareLeadId(
        ParameterContainer $parameterContainer,
        $existingIdentity,
        Locale $locale
    ) {
        $leadIdIncoming = $parameterContainer->get(ParameterName::getLeadId());

        if (
            null === $leadIdIncoming &&
            $existingIdentity instanceof IdentityModel &&
            $existingIdentity->getLatestLeadId() instanceof LeadId
        ) {
            return $existingIdentity->getLatestLeadId();
        }

        return LeadId::fromLocale($locale);
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param IdentityModel|null $existingIdentity
     * @return IdentityModel
     * @throws InvalidUuidException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws MissingRequiredParameterException
     */
    private function prepareIdentityByParameters(
        ParameterContainer $parameterContainer,
        IdentityModel $existingIdentity = null
    ) {
        $identity = $this->getIdentity($parameterContainer, $existingIdentity);

        $websiteTestParameter = $parameterContainer->get(
            ParameterName::getWebsiteTestParameter()
        );

        if (!empty($websiteTestParameter)) {
            $identity->addWebsiteTestParameter(
                TestParameter::fromString($websiteTestParameter)
            );
        }

        $salesForceParameter = $parameterContainer->get(self::SALESFORCE);

        if (!empty($salesForceParameter) && is_array($salesForceParameter)) {
            $salesForceLead = SalesForceLead::fromArray($salesForceParameter);
            $identity->addSalesForceLead($salesForceLead);
        }

        return $identity;
    }

    /**
     * @param $existingIdentity
     * @return IdentityId
     * @throws InvalidUuidException
     */
    private function prepareIdentityId($existingIdentity)
    {
        if (
            $existingIdentity instanceof IdentityModel &&
            $existingIdentity->getLatestLocation() instanceof Location
        ) {
            return $existingIdentity->getId();
        }

        return IdentityId::generate();
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param IdentityModel|null $existingIdentity
     * @return Locale
     * @throws InvalidArgumentException
     * @throws MissingRequiredParameterException
     */
    private function prepareLocale(
        ParameterContainer $parameterContainer,
        IdentityModel $existingIdentity = null
    ) {
        if (
            $existingIdentity instanceof IdentityModel &&
            $existingIdentity->getLatestLocation() instanceof Location &&
            !$parameterContainer->get(ParameterName::getLocale())
        ) {
            return $existingIdentity->getLatestLocation()->getLocale();
        }

        return Locale::fromIsoString($parameterContainer->get(
            ParameterName::getLocale())
        );
    }

    /**
     * @param ParameterContainer $parameters
     * @param Locale $locale
     * @return Location
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws MissingRequiredParameterException
     */
    private function prepareLocationByParametersAndLocale(
        ParameterContainer $parameters,
        Locale $locale
    ) {
        $apiResponse = $this->campaignService->getDetailsByParametersAndLocale(
            $parameters, $locale
        );

        $country = Country::fromIsoAndName(
            $locale->getCountry(), $apiResponse->getCountry()
        );

        $location = Location::createFromLocaleAndCountry($locale, $country);

        $location->setAddress(
            $this->getAddress($apiResponse)
        );

        $ipAddress = $parameters->get(ParameterName::getIpAddress());

        if (null !== $ipAddress) {
            $location->setIpAddress(IpAddress::fromString($ipAddress));
        }

        return $location;
    }

    /**
     * @param ParameterContainer $parameters
     * @param Locale $locale
     * @return Campaign
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws MissingRequiredParameterException
     */
    private function prepareCampaignByParametersAndLocale(
        ParameterContainer $parameters,
        Locale $locale
    ) {
        return Campaign::fromCampaignApiResponse(
            $this->campaignService->getDetailsByParametersAndLocale($parameters, $locale)
        );
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param $campaign
     * @return bool
     */
    private function invalidCampaign(ParameterContainer $parameterContainer, Campaign $campaign)
    {
        return null !== $campaign &&
            $campaign->getAct() &&
            !$parameterContainer->get(ParameterName::getAct());
    }

    /**
     * @param $apiResponse
     * @return Address
     */
    private function getAddress(CampaignApiResponse $apiResponse)
    {
        return Address::fromZipCodeAndCityAndRegion(
            $apiResponse->getGeoZipCode(),
            $apiResponse->getGeoCity(),
            $apiResponse->getGeoRegion()
        );
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param IdentityModel $existingIdentity
     * @return IdentityModel
     */
    private function getIdentity(ParameterContainer $parameterContainer, IdentityModel $existingIdentity)
    {
        try {
            $locale = $this->prepareLocale($parameterContainer, $existingIdentity);

            $identity = new IdentityModel(
                $this->prepareIdentityId($existingIdentity)
            );

            $identity->addLeadId(
                $this->prepareLeadId($parameterContainer, $existingIdentity, $locale)
            );

            $location = $this->prepareLocationByParametersAndLocale(
                $parameterContainer, $locale
            );

            $identity->addLocation($location);

            $identity->addCampaign(
                $this->getCampaign($parameterContainer, $identity, $locale)
            );

            $identity->addDevice(Device::fromParameterContainer($parameterContainer));
            $identity->addReferrer(Referrer::fromParameterContainer($parameterContainer));
            $identity->addCookieId(CookieId::fromParameterContainer($parameterContainer));

            return $identity;
        } catch (Exception $exception) {
            JsonResponse::unprocessableEntity();
        }
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param $identity
     * @param $locale
     * @return Campaign
     * @throws RuntimeException
     * @throws MissingRequiredParameterException
     * @throws InvalidArgumentException
     */
    private function getCampaign(
        ParameterContainer $parameterContainer,
        IdentityModel $identity,
        Locale $locale
    ) {
        $campaign = $identity->getCampaignByAct(
            $parameterContainer->get(ParameterName::getAct())
        );

        if (!($campaign instanceof Campaign)) {
            $campaign = $this->prepareCampaignByParametersAndLocale(
                $parameterContainer, $locale
            );
        }

        return $campaign;
    }
}
