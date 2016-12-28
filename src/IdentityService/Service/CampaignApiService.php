<?php
namespace IdentityService\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use IdentityService\Config\CampaignApiConfig;
use IdentityService\ValueObject\CampaignApiResponse;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\ParameterName;
use Olando\Exception\MissingRequiredParameterException;
use Olando\Helper\ValidatorTrait;
use Olando\Http\ParameterContainer;
use Olando\ValueObject\AuthHeader;
use RuntimeException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CampaignService
 * @package LeadHandler\Service
 */
class CampaignApiService
{
    use ValidatorTrait;

    /** @var CampaignApiConfig */
    protected $campaignApiConfig;

    /** @var Client */
    protected $client;

    /** @var AuthHeader */
    private $authHeader;

    /** @var array */
    private static $responses = [];

    /**
     * CampaignService constructor.
     * @param CampaignApiConfig $campaignApiConfig
     * @param Client $client
     */
    public function __construct(CampaignApiConfig $campaignApiConfig, Client $client)
    {
        $this->campaignApiConfig = $campaignApiConfig;
        $this->client = $client;
        $this->authHeader = $this->campaignApiConfig->getAuthHeader();
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param Locale $locale
     * @return CampaignApiResponse
     * @throws MissingRequiredParameterException
     * @throws RuntimeException
     */
    public function getDetailsByParametersAndLocale(
        ParameterContainer $parameterContainer,
        Locale $locale
    ) {
        $requestHash = spl_object_hash($parameterContainer);

        if (!array_key_exists($requestHash, self::$responses)) {
            self::$responses[$requestHash] = $this->getCampaignResponse(
                $parameterContainer, $locale
            );
        }

        return self::$responses[$requestHash];
    }

    /**
     * @param $path
     * @param array $params
     * @return ParameterContainer
     * @throws RuntimeException
     */
    private function performRequest($path, array $params = [])
    {
        $uri = $this->campaignApiConfig->getBasePath() . $path;

        $response = $this->client->request(
            Request::METHOD_GET, $uri, $this->getRequestOptions($params)
        );

        $responseParams = [];
        $jsonContent = $response->getBody()->getContents();

        if (!empty($jsonContent) && $this->isJson($jsonContent)) {
            $responseParams = $this->getResponseParameters($jsonContent);
        }

        return ParameterContainer::fromParameterBag(
            new ParameterBag($responseParams)
        );
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param Locale $locale
     * @return array
     */
    private function getParameters(ParameterContainer $parameterContainer, Locale $locale)
    {
        return [
            ParameterName::getCountry() => $locale->getCountry(),
            ParameterName::getLanguage() => $locale->getLanguage(),
            ParameterName::getIpAddress() => $parameterContainer->get(
                ParameterName::getIpAddress()
            ),
            ParameterName::getAct() => $parameterContainer->get(
                ParameterName::getAct()
            ),
            ParameterName::getReferrer() => $parameterContainer->get(
                ParameterName::getReferrer()
            ),
            ParameterName::getTargetUrl() => $parameterContainer->get(
                ParameterName::getTargetUrl()
            ),
        ];
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @param Locale $locale
     * @return CampaignApiResponse
     * @throws RuntimeException
     */
    private function getCampaignResponse(
        ParameterContainer $parameterContainer,
        Locale $locale
    ) {
        return CampaignApiResponse::fromParameterContainer(
            $this->performRequest(
                DIRECTORY_SEPARATOR . $this->campaignApiConfig->getEndpointCampaign(),
                $this->getParameters($parameterContainer, $locale)
            )
        );
    }

    /**
     * @param $jsonContent
     * @return mixed
     */
    private function getResponseParameters($jsonContent)
    {
        $responseParams = json_decode($jsonContent, true);
        if (!empty($responseParams['data'])) {
            $responseParams = $responseParams['data'];
            return $responseParams;
        }
        return $responseParams;
    }

    /**
     * @param array $params
     * @return array
     */
    private function getRequestOptions(array $params)
    {
        return [
            RequestOptions::SYNCHRONOUS => true,
            RequestOptions::HEADERS => $this->authHeader->toArray(),
            RequestOptions::QUERY => $params,
        ];
    }
}
