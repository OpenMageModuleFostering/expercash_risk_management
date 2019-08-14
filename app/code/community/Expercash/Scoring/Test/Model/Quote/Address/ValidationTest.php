<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Quote_Address_ValidationTest
    extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @return Expercash_Scoring_Helper_Quote_Address_Validation
     */
    protected function getValidationModel()
    {
        return Mage::getModel('expercash_scoring/quote_address_validation');
    }

    public function testIsValidCountry()
    {
        $address = Mage::getModel('sales/quote_address');
        $model   = $this->getValidationModel();
        $this->assertFalse($model->isValidCountry($address));
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
        $this->assertFalse($model->isValidCountry($address));
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
        $this->assertFalse($model->isValidCountry($address));
        $address->setCountry('US');
        $this->assertFalse($model->isValidCountry($address));
        $address->setCountry('DE');
        $this->assertTrue($model->isValidCountry($address));
    }


    public function testHasTermsAndConditionsConfirmed()
    {
        $request   = new Mage_Core_Controller_Request_Http();
        $modelMock = $this->getModelMock(
            'expercash_scoring/quote_address_validation',
            array('isValidCountry')
        );
        $modelMock->expects($this->any())
            ->method('isValidCountry')
            ->will($this->returnValue(true));
        $this->assertFalse(
            $modelMock->hasTermsAndConditionsConfirmed($request)
        );
        $request->setParams(array('billing' => array('street' => 1)));
        $this->assertFalse(
            $modelMock->hasTermsAndConditionsConfirmed($request)
        );


        $request->setParams(
            array(
                 'billing' => array(
                     'scoring_check_confirmation' => 'scoring_check_confirmation'
                 )
            )
        );
        $this->assertTrue(
            $modelMock->hasTermsAndConditionsConfirmed($request)
        );
    }


}