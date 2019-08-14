<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Block_Checkout_Onepage_Billing_Agreement
    extends Mage_Checkout_Block_Onepage_Abstract
{

    /**
     * retrieves the config model
     *
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * retrieves the terms and condition text for the current store
     *
     * @return string - the terms and conditions text
     */
    public function getTermsAndConditionsText()
    {
        return $this->getConfigModel()->getTermsAddition(
            $this->getQuote()->getStoreId()
        );
    }
}