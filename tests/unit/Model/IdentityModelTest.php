<?php

namespace IdentityService\Model;

use IdentityService\ValueObject\Campaign;
use IdentityService\ValueObject\CookieId;
use IdentityService\ValueObject\Device;
use IdentityService\ValueObject\IdentityId;
use IdentityService\ValueObject\LeadId;
use IdentityService\ValueObject\Locale;
use IdentityService\ValueObject\Location;
use IdentityService\ValueObject\Referrer;
use IdentityService\ValueObject\SalesForceLead;
use IdentityService\ValueObject\Website\TestParameter;
use Olando\Http\ParameterContainer;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class IdentityModelTest
 * @package unit\Model
 * @covers IdentityService\Model\IdentityModel
 * @uses   IdentityService\ValueObject\Campaign
 * @uses   IdentityService\ValueObject\Device
 * @uses   IdentityService\ValueObject\Location
 * @uses   IdentityService\ValueObject\Uuid
 * @uses   IdentityService\ValueObject\IdentityId
 * @uses   IdentityService\ValueObject\Locale
 * @uses   IdentityService\ValueObject\LeadId
 * @uses   IdentityService\ValueObject\Website\TestParameter
 * @uses   IdentityService\ValueObject\SalesForceLead
 * @uses   IdentityService\ValueObject\CookieId
 * @uses   IdentityService\ValueObject\ParameterName
 */
class IdentityModelTest extends \PHPUnit_Framework_TestCase
{
    public function testModelCreationAndGetIdResult()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel(
            $identityId,
            LeadId::fromLocaleAndIdentityId(Locale::fromIsoString('de_DE'), $identityId)
        );
        self::assertSame($identityId, $model->getId());
    }

    public function testAddersAndSettersAndGetters()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        self::assertSame($identityId->toString(), $model->getId()->toString());
    }

    public function testCanAddAndGetCampaign()
    {

        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);

        $campaign = $this->getMockWithoutInvokingTheOriginalConstructor(Campaign::class);
        /** @var Campaign $campaign */
        $model->addCampaign($campaign);

        self::assertTrue(is_array($model->getCampaigns()));
    }

    public function testCanAddAndGetDevice()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $device = $this->getMockWithoutInvokingTheOriginalConstructor(Device::class);

        /** @var Device $device */
        $model->addDevice($device);
        self::assertTrue(is_array($model->getDevices()));
    }

    public function testCanAddAndGetSalesForceLead()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $parameterBag = new ParameterBag();
        $parameterBag->add(['test' => 'data']);
        $parameterContainer = ParameterContainer::fromParameterBag($parameterBag);
        $model->addSalesForceLead(SalesForceLead::fromParameterContainer($parameterContainer));
        self::assertInstanceOf(SalesForceLead::class, $model->getLatestSalesForceLead());
    }

    public function testCanAddAndGetLocation()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $location = $this->getMockWithoutInvokingTheOriginalConstructor(Location::class);

        /** @var Location $location */
        $model->addLocation($location);

        self::assertTrue(is_array($model->getLocations()));
    }


    public function testCanAddAndGetCookieId()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $parameterBag = new ParameterBag();
        $parameterBag->add(['cookieId' => 'cookie-123-cookie']);
        $parameterContainer = ParameterContainer::fromParameterBag($parameterBag);

        $model->addCookieId(CookieId::fromParameterContainer($parameterContainer));

        self::assertInstanceOf(CookieId::class, $model->getCookieId());
    }

    public function testCanAddAndGetAddReferrer()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $referrer = $this->getMockWithoutInvokingTheOriginalConstructor(Referrer::class);

        /** @var Referrer $referrer */
        $model->addReferrer($referrer);

        self::assertTrue(is_array($model->getReferrers()));
    }

    public function testCanSerializeToJson()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);
        $jsonObject = json_decode(json_encode($model));
        self::assertSame($identityId->toString(), $jsonObject->id);
    }

    public function testCanAddAndGetWebsiteParameter()
    {
        $identityId = IdentityId::generate();
        $model = new IdentityModel($identityId);

        $model->addWebsiteTestParameter(TestParameter::fromString(''));
        self::assertEmpty($model->getWebsiteTestParameter());

        $testParam = 'expected-value';
        $model->addWebsiteTestParameter(TestParameter::fromString($testParam));
        $model->addWebsiteTestParameter(TestParameter::fromString($testParam));
        self::assertSame($testParam, $model->getWebsiteTestParameter()->toString());
    }

    public function testCanAddMultipleParameterAndGetWebsiteParameter()
    {
        $identityModel = new IdentityModel(
            IdentityId::generate()
        );
        $paramSecond = TestParameter::fromString('secondParameter');
        $identityModel->addWebsiteTestParameter($paramSecond);

        $identityModelExisting = new IdentityModel(IdentityId::generate());
        $parameter = TestParameter::fromString('firstParameter');
        $identityModelExisting->addWebsiteTestParameter($parameter);

        $identityMergeFirst = $identityModelExisting->mergeIdentity($identityModel);


        $identityModelThird = new IdentityModel(IdentityId::generate());
        $paramThird = TestParameter::fromString('thirdParameter');
        $identityModelThird->addWebsiteTestParameter($paramThird);

        $identity = $identityMergeFirst->mergeIdentity($identityModelThird);

        self::assertInstanceOf(IdentityModel::class, $identity);
        self::assertSame($identity->getWebsiteTestParameter(), $parameter);
        self::assertTrue($identity->getWebsiteTestParameter()->toString() === 'firstParameter,secondParameter,thirdParameter');
    }


    public function testCanAddMultipleSameParameterAndGetWebsiteParameter()
    {
        $identityModel = new IdentityModel(
            IdentityId::generate()
        );
        $paramSecond = TestParameter::fromString('same');
        $identityModel->addWebsiteTestParameter($paramSecond);

        $identityModelExisting = new IdentityModel(IdentityId::generate());
        $parameter = TestParameter::fromString('firstParameter');
        $identityModelExisting->addWebsiteTestParameter($parameter);

        $identityMergeFirst = $identityModelExisting->mergeIdentity($identityModel);

        $identityModelThird = new IdentityModel(IdentityId::generate());
        $paramThird = TestParameter::fromString('same');
        $identityModelThird->addWebsiteTestParameter($paramThird);

        $identity = $identityMergeFirst->mergeIdentity($identityModelThird);

        self::assertInstanceOf(IdentityModel::class, $identity);
        self::assertSame($identity->getWebsiteTestParameter(), $parameter);
        self::assertTrue($identity->getWebsiteTestParameter()->toString() === 'firstParameter,same');
    }


    public function testCanGetLeadId()
    {
        $identityId = IdentityId::generate();
        $leadId = 'usually-a-processed-lead-id';
        $model = new IdentityModel($identityId);
        $model->addLeadId(LeadId::fromString($leadId));
        self::assertSame($model->getLatestLeadId()->toString(), $leadId);
    }

    public function testCanMergeIdentity()
    {
        $identityModel = new IdentityModel(
            IdentityId::generate(),
            LeadId::fromString('usually-a-processed-lead-id')
        );
        $identityModelExisting = new IdentityModel(IdentityId::generate());

        $device = $this->getMockWithoutInvokingTheOriginalConstructor(Device::class);
        /** @var Device $device */
        $identityModel->addDevice($device);

        $campaign = $this->getMockWithoutInvokingTheOriginalConstructor(Campaign::class);
        /** @var Campaign $campaign */
        $identityModel->addCampaign($campaign);

        $referrer = $this->getMockWithoutInvokingTheOriginalConstructor(Referrer::class);
        /** @var Referrer $referrer */
        $identityModel->addReferrer($referrer);

        $location = $this->getMockWithoutInvokingTheOriginalConstructor(Location::class);
        /** @var Location $location */
        $identityModel->addLocation($location);

        $leadId = $this->getMockWithoutInvokingTheOriginalConstructor(LeadId::class);
        /** @var LeadId $leadId */
        $identityModel->addLeadId($leadId);

        $cookie = $this->getMockWithoutInvokingTheOriginalConstructor(CookieId::class);
        /** @var CookieId $cookie */
        $identityModel->addCookieId($cookie);

        $salesForceLead = $this->getMockWithoutInvokingTheOriginalConstructor(SalesForceLead::class);
        /** @var SalesForceLead $salesForceLead */
        $identityModel->addSalesForceLead($salesForceLead);

        $identityModel->addWebsiteTestParameter(TestParameter::fromString('test-value'));

        $identity = $identityModelExisting->mergeIdentity($identityModel);
        self::assertInstanceOf(IdentityModel::class, $identity);
    }

    public function testMergeIdentityWithoutNewCookieIdLeadId()
    {
        $identityModel = new IdentityModel(
            IdentityId::generate()
        );

        $identityModelExisting = new IdentityModel(IdentityId::generate());

        $identity = $identityModelExisting->mergeIdentity($identityModel);

        self::assertSame($identityModel->getLatestLeadId(), $identity->getLatestLeadId());
        self::assertSame($identityModel->getCookieId(), $identity->getCookieId());
    }

    public function testCanGetCampaignByAct()
    {
        $identityModel = new IdentityModel(
            IdentityId::generate(),
            LeadId::fromString('usually-a-processed-lead-id')
        );

        $act = '157';
        $campaign = $this->getMockWithoutInvokingTheOriginalConstructor(Campaign::class);
        $campaign->method('getAct')->willReturn($act);
        /** @var Campaign $campaign */
        $identityModel->addCampaign($campaign);
        self::assertSame($identityModel->getCampaignByAct($act)->getAct(), $act);
        self::assertNull($identityModel->getCampaignByAct('unknown-act'));
    }
}
