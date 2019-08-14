<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Config_Source_Scoring_ValueTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testToOptionArray()
    {
        /* @var $model Expercash_Scoring_Model_System_Config_Source_Scoring_Value */
        $model = Mage::getModel(
            'expercash_scoring/system_config_source_scoring_value'
        );
        $options = $model->toOptionArray();
        $this->assertEquals(4, count($options));
        $assertValues = array(
            "",
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE,
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE,
            Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE
        );
        $dataHelper   = Mage::helper('expercash_scoring/data');
        $assertLabels = array(
            $dataHelper->__("-- none --"),
            $dataHelper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_LABEL
            ),
            $dataHelper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_LABEL
            ),
            $dataHelper->__(
                Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_LABEL
            )
        );
        $cnt          = 0;
        foreach ($options as $option) {
            $this->assertEquals($assertValues[$cnt], $option['value']);
            $this->assertEquals($assertLabels[$cnt], $option['label']);
            ++$cnt;
        }
    }

    public function getValidValuesTest()
    {
        $expectedValues = array(
            self::RED_VALUE, self::YELLOW_VALUE, self::GREEN_VALUE,
            self::NO_TERMS_AND_CONDITIONS_VALUE
        );
        $model          = Mage::getModel(
            'expercash_scoring/system_config_source_scoring_value'
        );
        $this->assertEquals($expectedValues, $model->getValidValues());
    }

}