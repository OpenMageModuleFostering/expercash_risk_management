<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Helper_Request_AdapterTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected function registerConfigMock()
    {
        $configMock = $this->getModelMock(
            'expercash_scoring/config', array('getProjectId', 'getApiKey')
        );
        $configMock->expects($this->any())
            ->method('getProjectId')
            ->will($this->returnValue('4711'));
        $configMock->expects($this->any())
            ->method('getApiKey')
            ->will($this->returnValue('0815'));
        $this->replaceByMock('model', 'expercash_scoring/config', $configMock);
    }

    public function testConvert()
    {
        $this->registerConfigMock();
        /** @var $adapter Expercash_Scoring_Helper_Request_Params_Adapter */
        $adapter = Mage::helper('expercash_scoring/request_params_adapter');
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = Mage::getModel('sales/quote_address');
        $address->setFirstname('Max');
        $address->setLastname('Muster');
        $address->setStreet('An der Tabaksmühle 18a Hinterhaus');
        $address->setPostCode('04317');
        $address->setCity('Leipzig');
        $address->setCountry('DE');

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setBillingAddress($address);
        $result = $adapter->convert($quote);
        $this->assertionsForCommonData($result);
        $this->assertionsForBillingAddress(
            $result, $quote->getBillingAddress(), 'An der Tabaksmühle', '18a'
        );

        $address->setStreet('An der Tabaksmühle, Hinterhaus 18a');
        $quote->setBillingAddress($address);
        $result = $adapter->convert($quote);
        $this->assertionsForCommonData($result);
        $this->assertionsForBillingAddress(
            $result, $quote->getBillingAddress(),
            'An der Tabaksmühle, Hinterhaus', '18a'
        );

        $address->setStreet('An der Tabaksmühle, Hinterhaus');
        $quote->setBillingAddress($address);
        $result = $adapter->convert($quote);
        $this->assertionsForCommonData($result);
        $this->assertionsForBillingAddress(
            $result, $quote->getBillingAddress(),
            'An der Tabaksmühle, Hinterhaus', ''
        );

        $address->setStreet('M3 8');
        $quote->setBillingAddress($address);
        $result = $adapter->convert($quote);
        $this->assertionsForCommonData($result);
        $this->assertionsForBillingAddress(
            $result, $quote->getBillingAddress(), 'M3', '8'
        );

        $address->setStreet('Ægirsvej 4');
        $quote->setBillingAddress($address);
        $result = $adapter->convert($quote);
        $this->assertionsForCommonData($result);
        $this->assertionsForBillingAddress(
            $result, $quote->getBillingAddress(), 'Ægirsvej', '4'
        );

        $this->assertArrayNotHasKey('customer_gender', $result);
        $this->assertArrayNotHasKey('customer_date_of_birth', $result);
    }

    public function testConvertContainsCustomerGender()
    {
        $helperMock = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('getGenderText')
        );
        $helperMock->expects($this->any())
            ->method('getGenderText')
            ->will($this->returnValue('male'));
        $address = Mage::getModel('sales/quote_address');
        $quote   = Mage::getModel('sales/quote');
        $quote->setBillingAddress($address);
        $result = $helperMock->convert($quote);
        $this->assertArrayHasKey('customer_gender', $result);
        $this->assertEquals('m', $result['customer_gender']);

        $helperMock = $this->getHelperMock(
            'expercash_scoring/request_params_adapter', array('getGenderText')
        );
        $helperMock->expects($this->any())
            ->method('getGenderText')
            ->will($this->returnValue('female'));
        $result = $helperMock->convert($quote);
        $this->assertArrayHasKey('customer_gender', $result);
        $this->assertEquals('f', $result['customer_gender']);

    }

    public function testConvertContainsCustomerDob()
    {
        /** @var $adapter Expercash_Scoring_Helper_Request_Params_Adapter */
        $adapter = Mage::helper('expercash_scoring/request_params_adapter');
        /** @var $address Mage_Sales_Model_Quote_address */
        $address = Mage::getModel('sales/quote_address');
        /** @var $quote  Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setBillingAddress($address);
        $customer = Mage::getModel('customer/customer');
        $customer->setDob('1999-01-01');
        $quote->setCustomer($customer);
        $result = $adapter->convert($quote);
        $this->assertArrayHasKey('customer_date_of_birth', $result);
        $this->assertEquals('19990101', $result['customer_date_of_birth']);
    }

    protected function assertionsForCommonData($dataArray)
    {
        $this->assertArrayHasKey('pid', $dataArray);
        $this->assertArrayHasKey('pkey', $dataArray);
        $this->assertArrayHasKey('cref', $dataArray);

        $this->assertEquals('4711', $dataArray['pid']);
        $this->assertEquals('0815', $dataArray['pkey']);
    }

    protected function assertionsForBillingAddress(
        $dataArray, $billingAddress, $expectedStreet, $expectedStreetNumber
    )
    {
        $this->assertArrayHasKey('customer_prename', $dataArray);
        $this->assertArrayHasKey('customer_name', $dataArray);
        $this->assertArrayHasKey('customer_address1', $dataArray);
        $this->assertArrayHasKey('customer_address2', $dataArray);
        $this->assertArrayHasKey('customer_zip', $dataArray);
        $this->assertArrayHasKey('customer_city', $dataArray);
        $this->assertArrayHasKey('customer_country', $dataArray);

        $this->assertEquals(
            $billingAddress->getFirstname(), $dataArray['customer_prename']
        );
        $this->assertEquals(
            $billingAddress->getLastname(), $dataArray['customer_name']
        );
        $this->assertEquals($expectedStreet, $dataArray['customer_address1']);
        $this->assertEquals(
            $expectedStreetNumber, $dataArray['customer_address2']
        );
        $this->assertEquals(
            $billingAddress->getPostcode(), $dataArray['customer_zip']
        );
        $this->assertEquals(
            $billingAddress->getCity(), $dataArray['customer_city']
        );
        $this->assertEquals(
            $billingAddress->getCountry(), $dataArray['customer_country']
        );
    }


}