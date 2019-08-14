<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Adminhtml_Customer_Edit_Tab_Expercash_Expercash_Escore_ValueTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected $renderer;

    public function setUp()
    {
        $this->renderer
            = new Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Escore_Value();
        $column = new Varien_Object();
        $column->setIndex('escore');
        $this->renderer->setColumn($column);
    }

    public function testRender()
    {
        $helper = Mage::helper('expercash_scoring/data');
        $row = new Varien_Object();
        $row->setData(
            'escore',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore',
            'something different, but no valid scoring value'
        );
        $this->assertEquals(
            '', $this->renderer->render($row)
        );
    }

} 