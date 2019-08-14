<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Field_Country_Renderer
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    )
    {
        $element->setDisabled('disabled');

        return parent::_getElementHtml($element);
    }

} 