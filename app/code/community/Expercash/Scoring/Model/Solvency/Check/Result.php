<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Expercash_Scoring_Model_Solvency_Check_Result
    extends Mage_Core_Model_Abstract
{

    /**
     * Constructor
     *
     * @see lib/Varien/Varien_Object#_construct()
     * @return Expercash_Scoring_Model_Solvency_Check_Result
     */
    public function _construct()
    {
        $this->_init('expercash_scoring/solvency_check_result');
        parent::_construct();
    }


    /**
     * deletes entries for a given customer from the scoring result table
     *
     * @param      $customerId - the customer id we want to delete the scoring
     *                         information
     * @param null $entryId    - optional an entry id for a specific result to
     *                         delete
     */
    public function deleteForCustomer($customerId, $entryId = null)
    {
        $this->getCollection()->deleteForCustomer($customerId, $entryId);
    }


    /**
     * set the base scoring data
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param                        $scoringData
     */
    public function setScoringData(
        Mage_Sales_Model_Quote $quote, array $scoringData
    )
    {
        $this->setQuoteId($quote->getId());
        $customerId = $quote->getCustomer()->getId();
        if (0 < $customerId) {
            $this->setCustomerId($customerId);
        }
        if (array_key_exists('escore', $scoringData)) {
            $this->setEscore($scoringData['escore']);
        }
        if (array_key_exists('escore_feature', $scoringData)) {
            $this->setEscoreFeature($scoringData['escore_feature']);
        }
        if (array_key_exists('escore_value', $scoringData)) {
            $this->setEscoreValue($scoringData['escore_value']);
        }

    }

    /**
     * set the customer's address data
     *
     * @param array $addressData
     */
    public function setAddressData(array $addressData)
    {
        if (array_key_exists('customer_address1', $addressData)
            && strlen(trim($addressData['customer_address1'])) < 65
        ) {
            $this->setCustomerAddress1(trim($addressData['customer_address1']));
        }
        if (array_key_exists('customer_address2', $addressData)
            && strlen(trim($addressData['customer_address2'])) < 6
        ) {
            $this->setCustomerAddress2(trim($addressData['customer_address2']));
        }
        if (array_key_exists('customer_zip', $addressData)
            && strlen(trim($addressData['customer_zip'])) < 11
        ) {
            $this->setCustomerZip(trim($addressData['customer_zip']));
        }
        if (array_key_exists('customer_city', $addressData)
            && strlen(trim($addressData['customer_city'])) < 33
        ) {
            $this->setCustomerCity(trim($addressData['customer_city']));
        }
        if (array_key_exists('customer_country', $addressData)
            && strlen(trim($addressData['customer_country'])) < 3
        ) {
            $this->setCustomerCountry(trim($addressData['customer_country']));
        }
    }

    /**
     * sets the customer's data
     *
     * @param array $customerData
     */
    public function setCustomerData(array $customerData)
    {
        if (array_key_exists('customer_gender', $customerData)
            && strlen(trim($customerData['customer_gender'])) < 2
        ) {
            $this->setCustomerGender(trim($customerData['customer_gender']));
        }
        if (array_key_exists('customer_prename', $customerData)
            && strlen(trim($customerData['customer_prename'])) < 65
        ) {
            $this->setCustomerPrename(trim($customerData['customer_prename']));
        }
        if (array_key_exists('customer_name', $customerData)
            && strlen(trim($customerData['customer_name'])) < 65
        ) {
            $this->setCustomerName(trim($customerData['customer_name']));
        }
        if (array_key_exists('customer_date_of_birth', $customerData)
            && 8 == strlen(trim($customerData['customer_date_of_birth']))
            && ctype_digit(trim($customerData['customer_date_of_birth']))
        ) {
            $date = new Zend_Date(trim(
                $customerData['customer_date_of_birth']
            ), 'yyyyMMdd');
            $this->setCustomerDateOfBirth($date->get('yyyy-MM-dd'));
        }

    }

    /**
     * adds the customer id to the scoring data if given
     * @param int $scoringValueId - the scoring value id
     * @param int $customerId - the customer id
     */
    public function setCustomerToScoringValue($scoringValueId, $customerId)
    {
        if (is_numeric($scoringValueId) && 0 < $scoringValueId && 0 < $customerId) {
            $resultModel = $this->load($scoringValueId);
            if (0 < $resultModel->getCheckId()) {
                $resultModel->setCustomerId($customerId);
                $resultModel->save();
            }
        }
    }

}