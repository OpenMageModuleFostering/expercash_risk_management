<?php

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Expercash_Scoring_Test_Model_ConfigTest
    extends EcomDev_PHPUnit_Test_Case_Config
{

    protected $store = null;


    /**
     * sets the store for test execution
     */
    public function setUp()
    {
        $this->store = Mage::app()->getStore(0)->load(0);
    }

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    public function testIsActive()
    {
        $model = $this->getConfigModel();
        $this->assertFalse($model->isActive());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH . '/active';
        $this->store->setConfig($path, "1");
        $this->assertTrue($model->isActive());
        $this->assertFalse($model->isActive(1));
    }

    public function testGetProjectId()
    {
        $model = $this->getConfigModel();
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH . '/projectid';
        $this->store->setConfig($path, "4711");
        $this->assertEquals("4711", $model->getProjectId());
        $this->assertEquals(null, $model->getProjectId(1));
    }

    public function testGetApiKey()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getApiKey());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH . '/apikey';
        $this->store->setConfig($path, "4711-0815");
        $this->assertEquals("4711-0815", $model->getApiKey());
        $this->assertEquals(null, $model->getApiKey(1));

    }

    public function testTermsAddition()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getTermsAddition());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH . '/terms-addition';
        $this->store->setConfig($path, "my terms and conditions");
        $this->assertEquals(
            "my terms and conditions", $model->getTermsAddition()
        );
        $this->assertEquals(null, $model->getTermsAddition(1));
    }

    public function testGetTotalMin()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getTotalMin());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SOLVENCY_PATH . '/total-min';
        $this->store->setConfig($path, 1000);
        $this->assertEquals(1000, $model->getTotalMin());
        $this->assertEquals(null, $model->getTotalMin(1));
        $this->store->resetConfig();
    }

    public function testGetSkipForCustomerGroups()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(false, $model->getSkipForCustomerGroups());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SOLVENCY_PATH .
            '/skip-for-customer-groups';
        $skipCustomerGroups = array(1, 2);
        $this->store->setConfig($path, serialize($skipCustomerGroups));
        $this->assertEquals(
            $skipCustomerGroups, $model->getSkipForCustomerGroups()
        );
        $this->assertEquals(false, $model->getSkipForCustomerGroups(1));
    }

    public function testGetDefaultScoringIfNoDataReturned()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getDefaultScoringIfNoDataReturned());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SOLVENCY_PATH .
            '/default-scoring-if-no-data-returned';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getDefaultScoringIfNoDataReturned());
        $this->assertEquals(null, $model->getDefaultScoringIfNoDataReturned(1));

    }

    public function testGetMaxNumberOfChecksPerSession()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getMaxNumberOfChecksPerSession());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/max-number-of-checks-per-session';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getMaxNumberOfChecksPerSession());
        $this->assertEquals(null, $model->getMaxNumberOfChecksPerSession(1));

    }

    public function testGetDefaultScoringForSessionCount()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getDefaultScoringForSessionCount());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/default-scoring-for-session-count';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getDefaultScoringForSessionCount());
        $this->assertEquals(null, $model->getDefaultScoringForSessionCount(1));
    }

    public function testGetMaxNumberOfChecksPerDay()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getMaxNumberOfChecksPerDay());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/max-number-of-checks-per-day';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getMaxNumberOfChecksPerDay());
        $this->assertEquals(null, $model->getMaxNumberOfChecksPerDay(1));
    }

    public function testGetDefaultScoringForDayCount()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getDefaultScoringForDayCount());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/default-scoring-for-day-count';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getDefaultScoringForDayCount());
        $this->assertEquals(null, $model->getDefaultScoringForDayCount(1));
    }

    public function testIsScoringExpiringAfterDays()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->isScoringExpiringAfterDays());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/scoring-expiring-after-days';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->isScoringExpiringAfterDays());
        $this->assertEquals(null, $model->isScoringExpiringAfterDays(1));
    }

    public function testGetScoringRepeatAfterDays()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(null, $model->getScoringRepeatAfterDays());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::RE_REQUEST_PATH .
            '/scoring-repeat-after-days';
        $this->store->setConfig($path, 1);
        $this->assertEquals(1, $model->getScoringRepeatAfterDays());
        $this->assertEquals(null, $model->getScoringRepeatAfterDays(1));
    }

    public function testGetAllowedPaymentMethodsForScoringValueRed()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueRed()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SCORING_VALUE_RED_PATH .
            '/allowed-payment-methods';
        $allowPaymentMethods = array('credit card', 'direct debit');
        $this->store->setConfig($path, serialize($allowPaymentMethods));
        $this->assertEquals(
            $allowPaymentMethods,
            $model->getAllowedPaymentMethodsForScoringValueRed()
        );
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueRed(1)
        );
    }

    public function testGetAllowedPaymentMethodsForScoringValueYellow()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueYellow()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SCORING_VALUE_YELLOW_PATH .
            '/allowed-payment-methods';
        $allowPaymentMethods = array('credit card', 'direct debit');
        $this->store->setConfig($path, serialize($allowPaymentMethods));
        $this->assertEquals(
            $allowPaymentMethods,
            $model->getAllowedPaymentMethodsForScoringValueYellow()
        );
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueYellow(1)
        );
    }

    public function testGetAllowedPaymentMethodsForScoringValueGreen()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueGreen()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SCORING_VALUE_GREEN_PATH .
            '/allowed-payment-methods';
        $allowPaymentMethods = array('credit card', 'direct debit');
        $this->store->setConfig($path, serialize($allowPaymentMethods));
        $this->assertEquals(
            $allowPaymentMethods,
            $model->getAllowedPaymentMethodsForScoringValueGreen()
        );
        $this->assertEquals(
            array(), $model->getAllowedPaymentMethodsForScoringValueGreen(1)
        );
    }

    public function testGetAdditionalConditionForScoringValueGreen()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            false, $model->getAdditionalConditionForScoringValueGreen()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SCORING_VALUE_GREEN_PATH .
            '/additional-condition';
        $cond
            = Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE;
        $this->store->setConfig($path, $cond);
        $this->assertEquals(
            $cond, $model->getAdditionalConditionForScoringValueGreen()
        );
        $this->assertEquals(
            false, $model->getAdditionalConditionForScoringValueGreen(1)
        );
    }

    public function testGetAdditionalConditionForScoringValueYellow()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            false, $model->getAdditionalConditionForScoringValueYellow()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SCORING_VALUE_YELLOW_PATH .
            '/additional-condition';
        $cond
            = Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE;
        $this->store->setConfig($path, $cond);
        $this->assertEquals(
            $cond, $model->getAdditionalConditionForScoringValueYellow()
        );
        $this->assertEquals(
            false, $model->getAdditionalConditionForScoringValueYellow(1)
        );
    }

    public function testGetAllowedCountries()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(array('DE'), $model->getAllowedCountries());
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH .
            '/allowed-country';
        $allowedCountries = array('DE');
        $this->store->setConfig($path, serialize($allowedCountries));
        $this->assertEquals($allowedCountries, $model->getAllowedCountries());
        $this->assertEquals(array('DE'), $model->getAllowedCountries(1));
    }


    public function testGetAlwaysOfferTheFollowingPaymentMethods()
    {
        $model = $this->getConfigModel();
        $this->assertEquals(
            array(), $model->getAlwaysOfferTheFollowingPaymentMethods()
        );
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::SOLVENCY_PATH .
            '/always-offer-the-following-payment-methods';
        $allowPaymentMethods = array('credit card', 'direct debit');
        $this->store->setConfig($path, serialize($allowPaymentMethods));
        $this->assertEquals(
            $allowPaymentMethods,
            $model->getAlwaysOfferTheFollowingPaymentMethods()
        );
        $this->assertEquals(
            array(), $model->getAlwaysOfferTheFollowingPaymentMethods(1)
        );
    }

    public function testEventsAreDefined()
    {
        $this->assertEventObserverDefined(
            'frontend', 'core_block_abstract_to_html_after',
            'expercash_scoring/observer', 'appendAgreementToBilling'
        );
        $this->assertEventObserverDefined(
            'frontend', 'controller_action_postdispatch',
            'expercash_scoring/observer', 'performSolvencyCheck'
        );
        $this->assertEventObserverDefined(
            'frontend', 'core_block_abstract_prepare_layout_before',
            'expercash_scoring/observer', 'filterPaymentMethods'
        );
        $this->assertEventObserverDefined(
            'frontend', 'checkout_submit_all_after',
            'expercash_scoring/observer', 'setScoringValueToRegisteredUser'
        );
    }
}

