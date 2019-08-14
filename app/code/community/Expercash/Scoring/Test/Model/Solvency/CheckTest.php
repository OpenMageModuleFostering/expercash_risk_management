<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Solvency_CheckTest
    extends EcomDev_PHPUnit_Test_Case
{

    /**
     * max checks per day exceeded, use default as scoring
     */
    public function testPerformSolvencyCheckWithoutRequest()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(1));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('Y'));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = new Varien_Object();
        $fakeResultModel->setCollection($fakeCollection);
        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getResultModel',
                 'getCheckoutSession', 'saveSolvencyResult'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));
        $quote = Mage::getModel('sales/quote');
        $quote->setCustomerGroupId(1);
        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckWithRequest()
    {
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(3));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('Y'));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = new Varien_Object();
        $fakeResultModel->setCollection($fakeCollection);
        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getResultModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array('escore' => 'R')));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'R', $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckWithRequestWithException()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                 'getDefaultScoringIfNoDataReturned',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(3));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('Y'));

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getDefaultScoringIfNoDataReturned')
            ->will($this->returnValue(null));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = new Varien_Object();
        $fakeResultModel->setCollection($fakeCollection);
        $checkMock      = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getResultModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession', 'getDataHelper'
            )
        );
        $dataHelperMock = $this->getHelperMock(
            'expercash_scoring/data', array('log')
        );

        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array('escore' => 'R')));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->throwException(new Exception('not important')));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $dataHelperMock = $this->getHelperMock(
            'expercash_scoring/data', array('log')
        );
        $dataHelperMock->expects($this->once())
            ->method('log')
            ->with(
                $dataHelperMock->__(
                    'Excention during call to Expercash not important'
                )
            );
        $checkMock->expects($this->once())
            ->method('getDataHelper')
            ->will($this->returnValue($dataHelperMock));

        $this->assertEquals(
            null, $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckWithoutRequestIfChecksPerSessionExceeded(
    )
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerSession',
                 'getDefaultScoringForSessionCount',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerSession')
            ->will($this->returnValue(3));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForSessionCount')
            ->will($this->returnValue('Y'));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );

        $fakeSession = new Varien_Object();
        $fakeSession->setData('ExperCash_Scoring_Check_Cnt', 5);


        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue($fakeSession));

        $quote = Mage::getModel('sales/quote');
        $quote->setCustomerGroupId(1);
        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );
    }


    public function testPerformSolvencyCheckNoDataReturned()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                 'getDefaultScoringIfNoDataReturned',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(3));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('Y'));
        $configMock->expects($this->any())
            ->method('getDefaultScoringIfNoDataReturned')
            ->will($this->returnValue('G'));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getDefaultScoringIfNoDataReturned')
            ->will($this->returnValue('Y'));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = new Varien_Object();
        $fakeResultModel->setCollection($fakeCollection);
        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getResultModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');
        $quote->setCustomerGroupId(1);

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array()));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'G', $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckNoDataStillUnknown()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                 'getDefaultScoringIfNoDataReturned',
                 'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(3));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('Y'));
        $configMock->expects($this->any())
            ->method('getDefaultScoringIfNoDataReturned')
            ->will($this->returnValue(''));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getDefaultScoringIfNoDataReturned')
            ->will($this->returnValue('Y'));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = new Varien_Object();
        $fakeResultModel->setCollection($fakeCollection);
        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getResultModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');
        $quote->setCustomerGroupId(1);

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array()));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            null, $checkMock->performSolvencyCheck($quote, $request)
        );
    }


    public function testCustomerGroupSkipsCheck()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array('getSkipForCustomerGroups')
        );
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array(1, 2)));

        $amountValidationModel = $this->getModelMock(
            'expercash_scoring/quote_amount_validation',
            array('hasQuoteMinAmount')
        );
        $amountValidationModel->expects($this->once())
            ->method('hasQuoteMinAmount')
            ->will($this->returnValue(true));

        $checkModel = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getAmountValidationModel',
                 'getConfigModel'
            )
        );
        $checkModel->expects($this->once())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkModel->expects($this->once())
            ->method('getAmountValidationModel')
            ->will($this->returnValue($amountValidationModel));
        $checkModel->expects($this->once())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $quote = Mage::getModel('sales/quote');
        $quote->setCustomerId(1);
        $quote->setCustomerGroupId(1);
        $this->assertEquals(
            null, $checkModel->performSolvencyCheck($quote, $request)
        );


    }

    public function testGetConfigModel()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod('getConfigModel');
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof Expercash_Scoring_Model_Config
        );
    }

    public function testGetValidationModel()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod(
            'getAddressValidationModel'
        );
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof
            Expercash_Scoring_Model_Quote_Address_Validation
        );
    }

    public function testGetParamsAdapter()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod('getParamsAdapter');
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof
            Expercash_Scoring_Helper_Request_Params_Adapter
        );
    }

    public function testGetSolvencyCheckClient()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod(
            'getSolvencyCheckClient'
        );
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof
            Expercash_Scoring_Model_Solvency_Check_Client
        );
    }

    public function testGetSolvencyCheckResponse()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod(
            'getSolvencyCheckResponse'
        );
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof
            Expercash_Scoring_Model_Solvency_Check_Response
        );
    }

    public function testGetResultModel()
    {
        $model           = Mage::getModel('expercash_scoring/solvency_check');
        $reflectionClass = new ReflectionClass(get_class($model));
        $method          = $reflectionClass->getMethod('getResultModel');
        $method->setAccessible(true);
        $model = Mage::getModel('expercash_scoring/solvency_check');
        $this->assertTrue(
            $method->invoke($model) instanceof
            Expercash_Scoring_Model_Solvency_Check_Result
        );
    }

    public function testPerformSolvencyCheckWithDownGradeFromGreen()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueGreen'
            )
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValueGreen')
            ->will(
                $this->returnValue(
                    Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                )
            );


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array('escore' => 'G')));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );
    }


    public function testPerformSolvencyCheckWithDownGradeFromYellow()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueYellow'
            )
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValueYellow')
            ->will(
                $this->returnValue(
                    Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                )
            );


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array('escore' => 'Y')));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'R', $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckWithNoDownGradeForYellow()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueYellow'
            )
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValueYellow')
            ->will(
                $this->returnValue(
                    Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                )
            );


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->once())
            ->method('parseResponse')
            ->will(
                $this->returnValue(
                    array(
                         'escore'         => 'Y',
                         'escore_feature' => Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                    )
                )
            );

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );

    }

    public function testPerformSolvencyCheckWithNoDownGradeForRed()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                'getSkipForCustomerGroups',
                'getAdditionalConditionForScoringValueYellow'
            )
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValueYellow')
            ->will(
                $this->returnValue(
                    Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                )
            );


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                'getAddressValidationModel', 'getConfigModel',
                'getParamsAdapter', 'getSolvencyCheckClient',
                'getSolvencyCheckResponse', 'saveSolvencyResult',
                'getCheckoutSession', 'getAdditionalConditionForScoringValue'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));
        $checkMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValue')
            ->will($this->returnValue(array('fakeValue')));


        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->once())
            ->method('parseResponse')
            ->will(
                $this->returnValue(
                    array(
                        'escore'         => 'R',
                        'escore_feature' => Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                    )
                )
            );

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'R', $checkMock->performSolvencyCheck($quote, $request)
        );

    }

    public function testPerformSolvencyCheckWithNoDownGradeForYellowDueToHigherRating()
    {
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueYellow'
            )
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $configMock->expects($this->any())
            ->method('getAdditionalConditionForScoringValueYellow')
            ->will(
                $this->returnValue(
                    Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
                )
            );


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getParamsAdapter', 'getSolvencyCheckClient',
                 'getSolvencyCheckResponse', 'saveSolvencyResult',
                 'getCheckoutSession'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue(new Varien_Object()));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->once())
            ->method('parseResponse')
            ->will(
                $this->returnValue(
                    array(
                         'escore'         => 'Y',
                         'escore_feature' => Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE
                    )
                )
            );

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeResponse));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );

    }

    public function testValueIfNoTermsAndConditionsAreConfirmed()
    {
        $quote               = Mage::getModel('sales/quote');
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(false));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueYellow'
            )
        );

        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getCheckoutSession'
            )
        );

        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array('getSkipForCustomerGroups')
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));

        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $this->assertEquals(
            'U', $checkMock->performSolvencyCheck($quote, $request)
        );
    }


    public function testValueIfCustomerHasCheckWithinTheLastXDays()
    {
        $quote               = Mage::getModel('sales/quote');
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry')
        );

        $fakeResultEntry = $this->getResourceModelMock(
            'expercash_scoring/solvency_check_result_collection',
            array('getLastEntryFromTheLastDays')
        );
        $fakeResultEntry->expects($this->any())
            ->method('getLastEntryFromTheLastDays')
            ->will($this->returnValue('Y'));

        $fakeResultModel = $this->getModelMock(
            'expercash_scoring/solvency_check_result', array('getCollection')
        );
        $fakeResultModel->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($fakeResultEntry));

        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));

        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                 'getSkipForCustomerGroups',
                 'getAdditionalConditionForScoringValueYellow'
            )
        );

        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                 'getAddressValidationModel', 'getConfigModel',
                 'getCheckoutSession', 'getResultModel'
            )
        );

        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array('getSkipForCustomerGroups')
        );

        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));

        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));

        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));

        $this->assertEquals(
            'Y', $checkMock->performSolvencyCheck($quote, $request)
        );
    }

    public function testPerformSolvencyCheckWithUserRegistering()
    {
        $validationModelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry', 'hasTermsAndConditionsConfirmed')
        );
        $request             = new Mage_Core_Controller_Request_Http();
        $validationModelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $validationModelMock->expects($this->any())
            ->method('hasTermsAndConditionsConfirmed')
            ->will($this->returnValue(true));
        $configMock = $this->getModelMock(
            'expercash_scoring/config',
            array(
                'getMaxNumberOfChecksPerDay', 'getDefaultScoringForDayCount',
                'getSkipForCustomerGroups'
            )
        );
        $configMock->expects($this->any())
            ->method('getMaxNumberOfChecksPerDay')
            ->will($this->returnValue(40));
        $configMock->expects($this->any())
            ->method('getDefaultScoringForDayCount')
            ->will($this->returnValue('G'));
        $configMock->expects($this->any())
            ->method('getSkipForCustomerGroups')
            ->will($this->returnValue(array()));
        $fakeCollection = new Varien_Object();
        $fakeCollection->setEntriesForCurrentDay(2);
        $fakeResultModel = $this->getModelMock('expercash_scoring/solvency_check_result', array('save', 'getId'));
        $fakeResultModel->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(123));

        $fakeResultModel->setCollection($fakeCollection);

        $fakeSession = new Varien_Object();

        $fakeDataHelper = $this->getHelperMock('expercash_scoring/data', array('isUserRegistering'));
        $fakeDataHelper->expects($this->any())
            ->method('isUserRegistering')
            ->will($this->returnValue(true));


        $checkMock = $this->getModelMock(
            'expercash_scoring/solvency_check',
            array(
                'getAddressValidationModel', 'getConfigModel',
                'getResultModel',
                'getParamsAdapter', 'getSolvencyCheckClient',
                'getSolvencyCheckResponse',
                'getCheckoutSession', 'getDataHelper'
            )
        );
        $checkMock->expects($this->any())
            ->method('getAddressValidationModel')
            ->will($this->returnValue($validationModelMock));
        $checkMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($configMock));
        $checkMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResultModel));
        $checkMock->expects($this->any())
            ->method('getCheckoutSession')
            ->will($this->returnValue($fakeSession));
        $checkMock->expects($this->any())
            ->method('getDataHelper')
            ->will($this->returnValue($fakeDataHelper));

        $quote = Mage::getModel('sales/quote');

        $fakeResponse = $this->getModelMock(
            'expercash_scoring/solvency_check_response', array('parseResponse')
        );
        $fakeResponse->expects($this->any())
            ->method('parseResponse')
            ->will($this->returnValue(array('escore' => 'R')));

        $fakeAdapter = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('convert')
        );
        $fakeAdapter->expects($this->any())
            ->method('convert')
            ->will($this->returnValue(array()));

        $checkMock->expects($this->any())
            ->method('getParamsAdapter')
            ->will($this->returnValue($fakeAdapter));

        $fakeClient = $this->getModelMock(
            'expercash_scoring/solvency_check_client', array('postRequest')
        );
        $fakeClient->expects($this->any())
            ->method('postRequest')
            ->will($this->returnValue('not important'));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckClient')
            ->will($this->returnValue($fakeClient));

        $checkMock->expects($this->any())
            ->method('getSolvencyCheckResponse')
            ->will($this->returnValue($fakeResponse));

        $this->assertEquals(
            'R', $checkMock->performSolvencyCheck($quote, $request)
        );
        $this->assertEquals(123, $fakeSession->getExpScoringId());
    }
}