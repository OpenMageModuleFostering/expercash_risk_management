<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Checkout_Payment_Methods_FilterTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testFilterPaymentsPassMethodsWithInvalidScoringValue()
    {
        $quote           = Mage::getModel('sales/quote');
        $paymentMethods  = $this->getFakePaymentMethods();
        $scoringValue    = null;
        $scoringValue    = null;
        $filterModel     = Mage::getModel(
            'expercash_scoring/checkout_payment_methods_filter'
        );
        $filteredMethods = $filterModel->filterPaymentMethods(
            $quote, $paymentMethods, $scoringValue
        );
        $this->assertEquals($paymentMethods, $filteredMethods);
    }

    public function testFilterPaymentMethodsPassWithEmptyPaymentMethods()
    {
        $quote          = Mage::getModel('sales/quote');
        $paymentMethods = array();
        $scoringValue   = null;
        $scoringValue
                         = Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE;
        $filterModel     = Mage::getModel(
            'expercash_scoring/checkout_payment_methods_filter'
        );
        $filteredMethods = $filterModel->filterPaymentMethods(
            $quote, $paymentMethods, $scoringValue
        );
        $this->assertEquals($paymentMethods, $filteredMethods);
    }

    public function testFilterPaymentMethodsFiltersForRedValue()
    {
        $quote          = Mage::getModel('sales/quote');
        $paymentMethods = $this->getFakePaymentMethods();
        $scoringValue
                        = Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE;
        $fakeAllowedPms = array('checkmo');
        $configMock     = $this->getConfigMock(
            'getAllowedPaymentMethodsForScoringValueRed', $fakeAllowedPms
        );
        $filterMock     = $this->getFilterMock($configMock);
        $result         = $filterMock->filterPaymentMethods(
            $quote, $paymentMethods, $scoringValue
        );
        $this->assertTrue(array_key_exists('checkmo', $result));
        $this->assertFalse(array_key_exists('savedcc', $result));
        $this->assertFalse(array_key_exists('paypal', $result));
        $this->assertFalse(array_key_exists('ops_cc', $result));

    }

    public function testFilterPaymentMethodsFiltersForYellowValue()
    {
        $quote          = Mage::getModel('sales/quote');
        $paymentMethods = $this->getFakePaymentMethods();
        $scoringValue
                        = Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE;
        $fakeAllowedPms = array('savedcc', 'checkmo');
        $configMock     = $this->getConfigMock(
            'getAllowedPaymentMethodsForScoringValueYellow', $fakeAllowedPms
        );
        $filterMock     = $this->getFilterMock($configMock);
        $result         = $filterMock->filterPaymentMethods(
            $quote, $paymentMethods, $scoringValue
        );
        $this->assertTrue(array_key_exists('checkmo', $result));
        $this->assertTrue(array_key_exists('savedcc', $result));
        $this->assertFalse(array_key_exists('paypal', $result));
        $this->assertFalse(array_key_exists('ops_cc', $result));
    }

    public function testFilterPaymentMethodsFiltersForGreenValue()
    {
        $quote          = Mage::getModel('sales/quote');
        $paymentMethods = $this->getFakePaymentMethods();
        $scoringValue
                        = Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE;
        $fakeAllowedPms = array('savedcc', 'checkmo', 'paypal', 'ops_cc');
        $configMock     = $this->getConfigMock(
            'getAllowedPaymentMethodsForScoringValueGreen', $fakeAllowedPms
        );
        $filterMock     = $this->getFilterMock($configMock);
        $result         = $filterMock->filterPaymentMethods(
            $quote, $paymentMethods, $scoringValue
        );
        $this->assertTrue(array_key_exists('checkmo', $result));
        $this->assertTrue(array_key_exists('savedcc', $result));
        $this->assertTrue(array_key_exists('paypal', $result));
        $this->assertTrue(array_key_exists('ops_cc', $result));
    }

    protected function getFakePaymentMethods()
    {
        return array(
            'checkmo' => $this->getFakePaymentMethod('checkmo'),
            'savedcc' => $this->getFakePaymentMethod('savedcc'),
            'paypal'  => $this->getFakePaymentMethod('paypal'),
            'ops_cc'  => $this->getFakePaymentMethod('ops_cc')
        );
    }

    protected function getFakePaymentMethod($code)
    {
        $method = new Varien_Object();
        $method->setCode($code);

        return $method;
    }

    protected function getConfigMock($methodToMock, $mockedReturn)
    {
        $configMock = $this->getModelMock(
            'expercash_scoring/config', array($methodToMock)
        );
        $configMock->expects($this->any())
            ->method($methodToMock)
            ->will($this->returnValue($mockedReturn));

        return $configMock;
    }

    protected function getFilterMock($configMock)
    {
        $filterMock = $this->getModelMock(
            'expercash_scoring/checkout_payment_methods_filter',
            array('getConfigModel')
        );
        $filterMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        return $filterMock;
    }

    public function testGetConfigModel()
    {
        $model           = Mage::getModel(
            'expercash_scoring/checkout_payment_methods_filter'
        );
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod('getConfigModel');
        $method->setAccessible(true);
        $model = Mage::getModel(
            'expercash_scoring/checkout_payment_methods_filter'
        );
        $this->assertTrue(
            $method->invoke($model) instanceof Expercash_Scoring_Model_Config
        );
    }
} 