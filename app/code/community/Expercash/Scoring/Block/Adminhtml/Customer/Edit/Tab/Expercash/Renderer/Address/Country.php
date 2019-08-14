<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Address_Country
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param Varien_Object $row
     *
     * @return string - the country name for the country code, empty string if
     *                   the name couldn't be retrieved
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $countryName = '';
        if (strlen(trim($value)) == 2) {
            $options = Mage::getResourceModel('directory/country_collection')
                ->addCountryIdFilter($value)
                ->load()
                ->toOptionArray();
            $country = end($options);
            if (is_array($country) && array_key_exists('label', $country)) {
                $countryName = $country['label'];
            }
        }
        return $countryName;
    }

} 