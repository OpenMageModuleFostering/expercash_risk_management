<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Adminhtml_Customer_Edit_Tab_Expercash_Expercash_Customer_GenderTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function setUp()
    {
        $this->renderer
            = new Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Customer_Gender();
        $column = new Varien_Object();
        $column->setIndex('customer_gender');
        $this->renderer->setColumn($column);
    }

    public function testRender()
    {
        $row = new Varien_Object();
        $this->assertEquals('', $this->renderer->render($row));
        $row->setCustomerGender('m');
        $this->assertEquals(
            Mage::helper('expercash_scoring/data')->__(
                'expercash_scoring::male'
            ), $this->renderer->render($row)
        );
        $row->setCustomerGender('f');
        $this->assertEquals(
            Mage::helper('expercash_scoring/data')->__(
                'expercash_scoring::female'
            ), $this->renderer->render($row)
        );
        $row->setCustomerGender('v');
        $this->assertEquals(
            Mage::helper('expercash_scoring/data')->__(''),
            $this->renderer->render($row)
        );
    }

} 