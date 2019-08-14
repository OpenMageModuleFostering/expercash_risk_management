<?php

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Expercash_Scoring_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{

    public function testCheckIfUserIsRegistering()
    {
        $quote = new Varien_Object();
        $quote->setCheckoutMethod(
            Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
        );
        $sessionMock = $this->getModelMock(
            'checkout/session', array('getQuote', 'init', 'save')
        );
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));
        $this->replaceByMock('model', 'checkout/session', $sessionMock);


        $this->assertTrue(
            Mage::helper('expercash_scoring/data')->isUserRegistering()
        );

        $quote->setCheckoutMethod(
            Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN
        );
        $this->assertTrue(
            Mage::helper('expercash_scoring/data')->isUserRegistering()
        );

        $quote->setCheckoutMethod(
            Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST
        );
        $this->assertFalse(
            Mage::helper('expercash_scoring/data')->isUserRegistering()
        );

    }
} 