<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Customer_Gender
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * renderer for customer gender in the scoring grid
     *
     * @param Varien_Object $row
     *
     * @return string - the formatted customer gender, empty string if it couldn't
     *                      be retrieved
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('expercash_scoring/data');
        if (trim($value) == 'm') {
            return $helper->__('expercash_scoring::male');
        }
        if (trim($value) == 'f') {
            return $helper->__('expercash_scoring::female');
        }
        return '';
    }

} 