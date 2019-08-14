<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Quote_Address_Validation
{

    /* @var $configModel Expercash_Scoring_Model_Config */
    protected $configModel = null;

    /**
     * sets the config model
     *
     * @param Expercash_Scoring_Model_Config $configModel
     */
    public function setConfigModel(Expercash_Scoring_Model_Config $configModel)
    {
        $this->configModel = $configModel;
    }

    /**
     * gets the config model
     *
     * @return Expercash_Scoring_Model_Config|null
     */
    public function getConfigModel()
    {
        if (null === $this->configModel) {
            $this->setConfigModel(Mage::getModel('expercash_scoring/config'));
        }

        return $this->configModel;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $addressToCheck
     *
     * @return bool - true if it's valid for country, false if not or the address
     * type does not match
     */
    public function isValidCountry(
        Mage_Sales_Model_Quote_Address $addressToCheck, $storeId = null
    )
    {
        $result = false;
        // only need to check for billing addresses
        if ($addressToCheck->getAddressType()
            === Mage_Sales_Model_Quote_Address::TYPE_BILLING
        ) {
            $validCountries = $this->getConfigModel()->getAllowedCountries(
                $storeId
            );
            if (in_array($addressToCheck->getCountry(), $validCountries)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @return Expercash_Scoring_Model_Solvency_Check_Result
     */
    protected function getResultModel()
    {
        return Mage::getModel('expercash_scoring/solvency_check_result');
    }


    /**
     * returns the data helper
     *
     * @return Expercash_Scoring_Helper_Data
     */
    protected function getDataHelper()
    {
        return Mage::helper('expercash_scoring/data');
    }

    /**
     * validates the confirmation of terms and conditions
     *
     * @param Mage_Sales_Model_Quote            $quote   - current quote
     * @param Mage_Core_Controller_Request_Http $request - the current request
     *
     * @return bool - true if the terms and conditions are valid, false otherwise
     */
    public function hasTermsAndConditionsConfirmed(
        Mage_Core_Controller_Request_Http $request
    )
    {
        $requestParams = $request->getParam('billing');
        if ((!is_array($requestParams)
            || !array_key_exists(
                'scoring_check_confirmation', $requestParams
            ))
        ) {
            return false;
        }

        return true;
    }

}