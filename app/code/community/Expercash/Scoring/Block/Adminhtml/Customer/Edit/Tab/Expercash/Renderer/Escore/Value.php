<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Escore_Value
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * renders the Expercash scoring values to a human readable format
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('expercash_scoring/data');
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE) {
           return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_LABEL);
        }
        return '';
    }
} 