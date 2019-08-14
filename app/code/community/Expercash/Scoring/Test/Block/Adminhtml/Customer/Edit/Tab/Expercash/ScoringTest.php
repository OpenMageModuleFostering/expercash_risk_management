<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Adminhtml_Customer_Edit_Tab_Expercash_ScoringTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected $block = null;

    public function setUp()
    {
        $this->block
            = new Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Scoring();
    }

    public function testGetLabel()
    {
        $expectedLabel = Mage::helper('expercash_scoring/data')->__(
            'Scoring Information'
        );
        $this->assertEquals($expectedLabel, $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $expectedLabel = Mage::helper('expercash_scoring/data')->__(
            'Scoring Information'
        );
        $this->assertEquals($expectedLabel, $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->assertFalse($this->block->canShowTab());
        $customer = new Varien_Object();
        $customer->setId(0);
        Mage::register('current_customer', $customer);
        $this->assertFalse($this->block->canShowTab());
        $customer->setId(1);
        Mage::register('current_customer', $customer, true);
        $this->assertTrue($this->block->canShowTab());
        Mage::unregister('current_customer');
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->block->isHidden());
    }

    public function testGetAfter()
    {
        $this->assertEquals('orders', $this->block->getAfter());
    }

}
