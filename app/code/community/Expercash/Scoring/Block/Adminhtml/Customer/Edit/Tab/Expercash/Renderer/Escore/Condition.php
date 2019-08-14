<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Escore_Condition
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * renders the expercash 'escore_feature' values to a human readable wording
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('expercash_scoring/data');
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_UNDELIVERABLE_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_UNDELIVERABLE_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_KNOWN_NO_PERSONAL_INFO_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_KNOWN_NO_PERSONAL_INFO_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::NO_POSTAL_INFORMATION_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::NO_POSTAL_INFORMATION_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_BUT_DEAD_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_BUT_DEAD_LABEL);
        }
        if ($value == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_WRONG_VALUE) {
            return $helper->__(Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::ADDRESS_WRONG_LABEL);
        }
        return '';
    }

} 