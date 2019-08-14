<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Solvency_Check
{

    const Session_Checks_Cnt = 'ExperCash_Scoring_Check_Cnt';

    /**
     * @return Expercash_Scoring_Model_Quote_Address_Validation
     */
    protected function getAddressValidationModel()
    {
        return Mage::getModel('expercash_scoring/quote_address_validation');
    }

    /**
     * @return Expercash_Scoring_Helper_Request_Params_Adapter
     */
    protected function getParamsAdapter()
    {
        return Mage::helper('expercash_scoring/request_params_adapter');
    }

    /**
     * @return Expercash_Scoring_Model_Solvency_Check_Client
     */
    protected function getSolvencyCheckClient()
    {
        return Mage::getModel('expercash_scoring/solvency_check_client');
    }

    /**
     * @return Expercash_Scoring_Model_Solvency_Check_Response
     */
    protected function getSolvencyCheckResponse()
    {
        return Mage::getModel('expercash_scoring/solvency_check_response');
    }

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * @return Expercash_Scoring_Model_Solvency_Check_Result
     */
    protected function getResultModel()
    {
        return Mage::getModel('expercash_scoring/solvency_check_result');
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return Expercash_Scoring_Model_Quote_Amount_Validation
     */
    protected function getAmountValidationModel()
    {
        return Mage::getModel('expercash_scoring/quote_amount_validation');
    }

    /**
     * @return Expercash_Scoring_Helper_Data
     */
    protected function getDataHelper()
    {
        return Mage::helper('expercash_scoring/data');
    }

    /**
     * performs the solvency check
     *
     * @param Mage_Sales_Model_Quote            $quote   - current quote
     * @param Mage_Core_Controller_Request_Http $request - current request
     *
     * @return string| null the scoring value for the customer or null if the check is not performed
     */
    public function performSolvencyCheck(
        Mage_Sales_Model_Quote $quote,
        Mage_Core_Controller_Request_Http $request
    )
    {
        /** @var $validationModel Expercash_Scoring_Model_Quote_Address_Validation */
        $validationModel    = $this->getAddressValidationModel();
        $amountValidation   = $this->getAmountValidationModel();
        $returnValue        = null;
        $storeId            = $quote->getStoreId();
        $dataHelper         = $this->getDataHelper();
        $configModel        = $this->getConfigModel();
        $customerGroup      = $quote->getCustomerGroupId();
        $skipCheckForGroups = $configModel->getSkipForCustomerGroups(
            $quote->getStoreId()
        );
        if ($validationModel->isValidCountry(
                $quote->getBillingAddress(), $storeId
            )
            && $amountValidation->hasQuoteMinAmount($quote)
            && !in_array($customerGroup, $skipCheckForGroups, true)
        ) {

            /*
             * if the customer has a solvency check result within
             * the last X days and this option is active, use last known
             * result
             */
            $returnValue = $this->getResultModel()->getCollection()
                ->getLastEntryFromTheLastDays($quote, $configModel);
            if (null !== $returnValue) {
                return $returnValue;
            }

            if (false === $validationModel->hasTermsAndConditionsConfirmed(
                $request
            )
            ) {
                return Expercash_Scoring_Model_System_Config_Source_Scoring_Value::NO_TERMS_AND_CONDITIONS_VALUE;
            }

            /*
             * return default scoring if number of allowed checks per session
             * is exceeded
             */
            if ($this->isSessionCountExceeded($storeId)) {
                return $configModel->getDefaultScoringForSessionCount($storeId);
            }

            /*
             * return default scoring if number of allowed checks per day
             * is exceeded
             */
            if ($this->isDayCountExceeded($storeId)) {
                return $this->getConfigModel()->getDefaultScoringForDayCount(
                    $storeId
                );
            }

            if (null === $returnValue) {
                try {
                    $checksSessionCnt = $this->getChecksSessionCnt();
                    $returnValue = $this->doRequest($quote);
                    Mage::log($returnValue, null, 'returnValue.log');
                    $this->getCheckoutSession()->setData(
                        self::Session_Checks_Cnt, ++$checksSessionCnt
                    );
                } catch (Exception $e) {
                    $message = sprintf(
                        'Excention during call to Expercash %s',
                        $e->getMessage()
                    );
                    $dataHelper->log($dataHelper->__($message));
                }
            }
            $defaultValue = $configModel->getDefaultScoringIfNoDataReturned(
                $quote->getStoreId()
            );
            if (null === $returnValue) {
                $returnValue = $defaultValue;
            }
        }

        return $returnValue;
    }

    /**
     * calls Expercash API for the solvency check
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return string - the scoring value or null if not given
     */
    protected function doRequest(Mage_Sales_Model_Quote $quote)
    {
        /** @var $adapter Expercash_Scoring_Helper_Request_Params_Adapter */
        $returnValue   = null;
        $adapter       = $this->getParamsAdapter();
        $requestParams = $adapter->convert($quote);
        /** @var $client Expercash_Scoring_Model_Solvency_Check_Client */
        $client   = $this->getSolvencyCheckClient();
        $response = $client->postRequest($requestParams);
        $result   = $this->getSolvencyCheckResponse()->parseResponse($response);
        if (array_key_exists('escore', $result)) {
            $returnValue = $result['escore'];
        }
        $returnValue = $this->getResultFromAdditionalCondition(
            $quote, $result, $returnValue
        );
        // save corrected data to database
        $result['escore'] = $returnValue;
        $this->saveSolvencyResult($result, $quote);
        return $returnValue;
    }

    /**
     * saves the solvency request result
     *
     * @param array                  $result
     * @param Mage_Sales_Model_Quote $quote
     *
     */
    protected function saveSolvencyResult(
        array $result, Mage_Sales_Model_Quote $quote
    )
    {
        $model = $this->getResultModel();
        $model->setScoringData($quote, $result);
        $model->setAddressData($result);
        $model->setCustomerData($result);
        $model->save();
        if ($this->getDataHelper()->isUserRegistering()) {
            $this->getCheckoutSession()->setData('exp_scoring_id', $model->getId());
        }
    }

    /**
     * checks whether an additional condition needs to be applied
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param array                  $response
     * @param                        $result
     *
     * @return string - the new scoring value
     */
    protected function getResultFromAdditionalCondition(
        Mage_Sales_Model_Quote $quote, array $response, $result
    )
    {
        $additionalConditions = $this->getAdditionalConditionForScoringValue(
            $quote, $result
        );
        if (0 < count($additionalConditions)
            && (!array_key_exists('escore_feature', $response)
                || !in_array($response['escore_feature'], $additionalConditions)
            )
        ) {
            $result = $this->getNextScoringValue($result);
        }

        return $result;
    }

    /**
     * retrieves if there must be additional information provided in the
     * expercahs response
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param                        $scoringValue
     *
     * @return array - the additional conditions for the given scoring value
     */
    protected function getAdditionalConditionForScoringValue(
        Mage_Sales_Model_Quote $quote, $scoringValue
    )
    {
        $result = array();
        $condition = '';
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE
        ) {
            $condition = $this->getConfigModel()
                ->getAdditionalConditionForScoringValueGreen(
                    $quote->getStoreId()
                );
        }
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE
        ) {
            $condition = $this->getConfigModel()
                ->getAdditionalConditionForScoringValueYellow(
                    $quote->getStoreId()
                );

        }
        // if a condition is defined we need it for further processing
        if (0 < strlen(trim($condition))) {
            $result[] = $condition;
        }
        // add higher level rating to result set in order not to get the wrong scoring value
        if ($condition == Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE) {
            $result[] = Expercash_Scoring_Model_System_Config_Source_Scoring_Condition::PERSON_KNOWN_VALUE;
        }

        return $result;
    }

    /**
     * retrieves the next lower scoring value for a given scoring value
     *
     * @param $scoringValue - the scoring value the next lower value is to be retrieved
     *
     * @return string - the next lower scoring value or the original scoring value if not a green or yellow one
     */
    protected function getNextScoringValue($scoringValue)
    {
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE
        ) {
            return Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE;
        }
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE
        ) {
            return Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE;
        }

        return $scoringValue;
    }

    /**
     * @param $checkoutSession
     *
     * @return int
     */
    protected function getChecksSessionCnt()
    {
        $checksSessionCnt = $this->getCheckoutSession()->getData(
            self::Session_Checks_Cnt
        );

        // initialize count for checks per session if needed
        if (!is_numeric($checksSessionCnt)) {
            $checksSessionCnt = 0;
        }

        return $checksSessionCnt;
    }

    /**
     * checks if the configured session count is exceeded or not
     *
     * @return bool- true if exceeded, false otherwise
     */
    protected function isSessionCountExceeded($storeId = null)
    {
        $checkoutSession  = $this->getCheckoutSession();
        $checksSessionCnt = $this->getChecksSessionCnt($checkoutSession);

        $allowChecksSession = $this->getConfigModel()
            ->getMaxNumberOfChecksPerSession(
                $storeId
            );

        return (is_numeric($allowChecksSession)
            && $allowChecksSession < $checksSessionCnt);
    }

    /**
     * checks if the allowed checks per day are exceeded
     *
     * @return bool - true if the allowed checks per day are exceeded,
     *  false otherwise
     */
    public function isDayCountExceeded($storeId = null)
    {
        $allowedChecksPerDay  = $this->getConfigModel()
            ->getMaxNumberOfChecksPerDay(
                $storeId
            );
        $entriesForCurrentDay = $this->getResultModel()->getCollection()
            ->getEntriesForCurrentDay();

        /*
         * return default scoring if number of allowed checks per day
         * is exceeded
         */

        return (0 < $allowedChecksPerDay
            && $allowedChecksPerDay < $entriesForCurrentDay);
    }

}