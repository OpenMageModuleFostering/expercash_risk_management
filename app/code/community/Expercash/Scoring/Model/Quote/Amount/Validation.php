<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Quote_Amount_Validation
{

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * checks whether the quote has the required minimum amount or not
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return bool
     */
    public function hasQuoteMinAmount(Mage_Sales_Model_Quote $quote)
    {
        $result = true;
        $minTotal = $this->getConfigModel()->getTotalMin($quote->getStoreId());
        if (is_numeric($minTotal) && 0 < $minTotal) {
            $result = ($minTotal < $quote->getBaseGrandTotal());
        }
        return $result;
    }

} 