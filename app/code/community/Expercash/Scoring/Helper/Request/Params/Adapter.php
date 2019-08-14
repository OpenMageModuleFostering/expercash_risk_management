<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Helper_Request_Params_Adapter
{

    /**
     * retrieves the necessary parameter from the quote
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array - the request parameter
     */
    public function convert(Mage_Sales_Model_Quote $quote)
    {
        $params = $this->getCommonParams($quote);
        /** @var $billingAddress Mage_Sales_Model_Quote_Address */
        $billingAddress = $quote->getBillingAddress();
        $params['customer_prename'] = $billingAddress->getFirstname();
        $params['customer_name'] = $billingAddress->getLastname();
        $streetArray = $this->splitStreet($billingAddress->getStreetFull());
        $params['customer_address1'] = $streetArray['street_name'];
        $params['customer_address2'] = $streetArray['street_number'];
        $params['customer_zip'] = $billingAddress->getPostcode();
        $params['customer_city'] = $billingAddress->getCity();
        $params['customer_country'] = $billingAddress->getCountry();
        $gender = $this->getCustomerGender($quote);
        if (0 < strlen(trim($gender))) {
            $params['customer_gender'] = $gender;
        }
        $dob = $this->getCustomerDob($quote);
        if (null !== $dob) {
            $params['customer_date_of_birth'] = $dob;
        }
        return $params;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array - the common params
     */
    protected function getCommonParams(Mage_Sales_Model_Quote $quote)
    {
        $storeId = $quote->getStoreId();
        $config = $this->getConfigModel();
        $params = array();
        $params['pid'] = $config->getProjectId($storeId);
        $params['pkey'] = $config->getApiKey($storeId);
        $params['cref'] = date('dmYGis') . '-' . $quote->getId();
        $params['action'] = 'risk_mgt';

        return $params;
    }

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * split street into street name, number and care of
     *
     * @param string $street
     *
     * @return array
     */
    protected function splitStreet($street)
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | care_of                 | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */
        if (preg_match(
            "/^([^0-9]+)([0-9]+[ ])*[ \t]*([0-9]*[-\w^.]*)?[, \t]*([^0-9]+.*)?\$/",
            $street, $matches
        )
        ) {

            //check if street has additional value and add it to streetname
            if (preg_match("/^([0-9]+)?\$/", trim($matches[2]))) {
                $matches[1] = $matches[1] . $matches[2];

            }
            return array(
                'street_name'   => trim($matches[1]),
                'street_number' => isset($matches[3]) ? $matches[3] : '',
            );
        }
        return array(
            'street_name'   => $street,
            'street_number' => '',
        );
    }

    /**
     * Tries to guess customers gender in expercash required form (f || m)
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return string
     */
    protected function getCustomerGender(Mage_Sales_Model_Quote $quote)
    {
        $result = '';
        $prefix = strtolower(
            Mage::helper('expercash_scoring/data')->coalesce(
                $this->getGenderText($quote->getBillingAddress(), 'gender'),
                $this->getGenderText($quote, 'customer_gender'),
                $this->getGenderText($quote->getCustomer(), 'gender'),
                $quote->getBillingAddress()->getPrefix(),
                $quote->getCustomerPrefix(),
                $quote->getCustomer()->getPrefix()
            )
        );
        if (0 < strlen(trim($prefix))) {
            $result = 'm';
            if (in_array(
                $prefix,
                array('mrs.', 'mrs', 'frau', 'fr.', 'fr', 'fräulein',
                      'frau dr.',
                      'female')
            )
            ) {
                $result = 'f';
            }
        }
        return $result;
    }

    /**
     * Retrive text of gender attribute of given entity.
     *
     * @param Mage_Core_Model_Abstract $entity
     * @param string                   $attributeCode
     *
     * @return string
     */
    protected function getGenderText($entity, $attributeCode)
    {
        return Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'gender')
            ->getSource()
            ->getOptionText($entity->getData($attributeCode));
    }

    /**
     * Get formated date of birth of customer, if not set return null.
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return string - the dob of the customer or null if none is set
     */
    protected function getCustomerDob(Mage_Sales_Model_Quote $quote)
    {
        $dob = null;
        $dob = $quote->getCustomerDob();
        if (!$dob) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $quote->getCustomer();
            if (!$customer || !$customer->getDob()) {
                return null;
            }
            $dob = $customer->getDob();
        }
        $date = new Zend_Date(strtotime($dob));
        return $date->get('yyyyMMdd');
    }
}