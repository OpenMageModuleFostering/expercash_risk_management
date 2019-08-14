<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Quote_Amount_ValidationTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testHasMinAmount()
    {
        $quote = Mage::getModel('sales/quote');
        $fakeConfig = $this->getModelMock(
            'expercash_scoring/config', array('getTotalMin')
        );
        $fakeConfig->expects($this->any())
            ->method('getTotalMin')
            ->will($this->returnValue(null));
        $quote->setBaseGrandTotal(5000.01);
        $this->assertTrue(
            Mage::getModel('expercash_scoring/quote_amount_validation')
                ->hasQuoteMinAmount($quote)
        );

        $modelMock = $this->getModelMock(
            'expercash_scoring/quote_amount_validation', array('getConfigModel')
        );
        $fakeConfig = $this->getModelMock(
            'expercash_scoring/config', array('getTotalMin')
        );
        $fakeConfig->expects($this->any())
            ->method('getTotalMin')
            ->will($this->returnValue(5000));
        $quote->setBaseGrandTotal(5000.01);

        $modelMock->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($fakeConfig));
        $this->assertTrue($modelMock->hasQuoteMinAmount($quote));

        $quote->setBaseGrandTotal(4999.99);
        $this->assertFalse($modelMock->hasQuoteMinAmount($quote));
    }

} 