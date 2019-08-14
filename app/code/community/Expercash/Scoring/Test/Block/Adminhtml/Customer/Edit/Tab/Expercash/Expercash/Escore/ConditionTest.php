<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Adminhtml_Customer_Edit_Tab_Expercash_Expercash_Escore_ConditionTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected $renderer;

    public function setUp()
    {
        $this->renderer
                = new Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Escore_Condition();
        $column = new Varien_Object();
        $column->setIndex('escore_feature');
        $this->renderer->setColumn($column);
    }

    public function testRender()
    {
        $helper = Mage::helper('expercash_scoring/data');
        $row    = new Varien_Object();
        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_LABEL
            ), $this->renderer->render($row)
        );
        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_UNDELIVERABLE_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_UNDELIVERABLE_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_KNOWN_NO_PERSONAL_INFO_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_KNOWN_NO_PERSONAL_INFO_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::NO_POSTAL_INFORMATION_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::NO_POSTAL_INFORMATION_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_BUT_DEAD_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_BUT_DEAD_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_WRONG_VALUE
        );
        $this->assertEquals(
            $helper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_WRONG_LABEL
            ), $this->renderer->render($row)
        );

        $row->setData(
            'escore_feature',
            'something different, but no expercash escore value'
        );
        $this->assertEquals(
            '', $this->renderer->render($row)
        );
    }

} 