<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Address_Street
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * formats street and house number
     *
     * @param Varien_Object $row
     *
     * @return string - the formatted street with house number
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (null !== $row->getData('customer_address2')) {
            $value .= ' ' . $row->getData('customer_address2');
        }
        return $value;
    }
} 