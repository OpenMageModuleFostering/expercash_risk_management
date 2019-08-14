<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Config_Source_Scoring_ConditionTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testToOptionArray()
    {
        /* @var $model Expercash_Scoring_Model_System_Config_Source_Scoring_Value */
        $model = Mage::getModel(
            'expercash_scoring/system_config_source_scoring_condition'
        );
        $options = $model->toOptionArray();
        $this->assertEquals(3, count($options));
        $assertValues = array(
            "",
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE,
            Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE
        );
        $dataHelper   = Mage::helper('expercash_scoring/data');
        $assertLabels = array(
            $dataHelper->__("-- none --"),
            $dataHelper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_LABEL
            ),
            $dataHelper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_LABEL
            ),
        );
        $cnt          = 0;
        foreach ($options as $option) {
            $this->assertEquals($assertValues[$cnt], $option['value']);
            $this->assertEquals($assertLabels[$cnt], $option['label']);
            ++$cnt;
        }
    }

}