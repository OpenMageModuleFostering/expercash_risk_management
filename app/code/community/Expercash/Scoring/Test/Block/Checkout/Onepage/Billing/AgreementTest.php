<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Checkout_Onepage_Billing_AgreementTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected $store = null;

    protected function getAgreementBlockMock($storeIdToReturn = null)
    {
        $fakeQuote = new Varien_Object();
        $fakeQuote->setStoreId($storeIdToReturn);
        $blockMock = $this->getBlockMock(
            'expercash_scoring/checkout_onepage_billing_agreement', array('getQuote')
        );
        $blockMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($fakeQuote));
        return $blockMock;
    }

    public function testGetTermsAndConditionsText()
    {
        $this->store = Mage::app()->getStore(0)->load(0);
        $path = Expercash_Scoring_Model_Config::SCORING_PATH . '/' .
            Expercash_Scoring_Model_Config::GENERAL_PATH . '/terms-addition';
        $this->store->setConfig($path, "my terms and conditions");
        $blockMock = $this->getAgreementBlockMock();
        $this->assertEquals(
            'my terms and conditions', $blockMock->getTermsAndConditionsText(0)
        );

        $this->store = Mage::app()->getStore(0)->load(1);

        $this->store->setConfig($path, "my terms and conditions 2");
        $this->assertEquals(
            'my terms and conditions 2',
            $blockMock->getTermsAndConditionsText(1)
        );
    }

}