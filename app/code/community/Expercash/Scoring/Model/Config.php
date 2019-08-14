<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Expercash_Scoring_Model_Config
{

    const SCORING_PATH = 'expercash_scoring';

    const GENERAL_PATH = 'general';

    const SOLVENCY_PATH = 'solvency';

    const RE_REQUEST_PATH = 're-request';

    const SCORING_VALUE_RED_PATH = 'scoring-value-red';

    const SCORING_VALUE_YELLOW_PATH = 'scoring-value-yellow';

    const SCORING_VALUE_GREEN_PATH = 'scoring-value-green';

    /**
     * @param string $path    - the path which stores the config data
     * @param null   $storeId - the storeId we want the config data for
     *
     * @return bool|mixed - false if path does not exist, the corresponding
     * value otherwise
     */
    protected function getConfigData($path, $storeId = null)
    {
        if (!empty($path)) {
            return Mage::getStoreConfig($path, $storeId);
        }
        return false;
    }

    /**
     * retrieves the config value for a given field from the general group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromGeneralGroup($fieldName, $storeId)
    {
        if (empty($fieldName)) {
            return false;
        }
        $path
            = self::SCORING_PATH . '/' . self::GENERAL_PATH . '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves the config value for a given field from the solvency group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromSolvencyGroup($fieldName, $storeId)
    {
        if (empty($fieldName)) {
            return false;
        }
        $path
            = self::SCORING_PATH . '/' . self::SOLVENCY_PATH . '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves the config value for a given field from the re-request group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromReRequestGroup($fieldName, $storeId)
    {
        if (empty($fieldName)) {
            return false;
        }
        $path
            =
            self::SCORING_PATH . '/' . self::RE_REQUEST_PATH . '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves the config value for a given field from the scoring value red
     * group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromScoringValueRedGroup(
        $fieldName, $storeId
    )
    {
        if (empty($fieldName)) {
            return false;
        }
        $path = self::SCORING_PATH . '/' . self::SCORING_VALUE_RED_PATH .
            '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves the config value for a given field from the scoring value
     * yellow group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromScoringValueYellowGroup(
        $fieldName, $storeId
    )
    {
        if (empty($fieldName)) {
            return false;
        }
        $path = self::SCORING_PATH . '/' . self::SCORING_VALUE_YELLOW_PATH .
            '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves the config value for a given field from the scoring value green
     * group
     *
     * @param $fieldName - the field which value we want to retrieve
     * @param $storeId   - the store we want the value for
     *
     * @return bool|mixed - false if path is empty or does not exists,
     *  the corresponding value otherwise
     */
    protected function getConfigDataFromScoringValueGreenGroup(
        $fieldName, $storeId
    )
    {
        if (empty($fieldName)) {
            return false;
        }
        $path = self::SCORING_PATH . '/' . self::SCORING_VALUE_GREEN_PATH .
            '/' . $fieldName;
        return $this->getConfigData($path, $storeId);
    }

    /**
     * retrieves if the module is active for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return bool - true if the module is activated, false otherwise
     */
    public function isActive($storeId = null)
    {
        return (bool)$this->getConfigDataFromGeneralGroup('active', $storeId);
    }

    /**
     * retrieves the project id for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return string|bool string if the path exists, false otherwise
     */
    public function getProjectId($storeId = null)
    {
        return $this->getConfigDataFromGeneralGroup('projectid', $storeId);
    }

    /**
     * retrieves the api key for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return string|bool string if the path exists, false otherwise
     */
    public function getApiKey($storeId = null)
    {
        return $this->getConfigDataFromGeneralGroup('apikey', $storeId);
    }

    /**
     * retrieves the terms addition for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return string|bool string if the path exists, false otherwise
     */
    public function getTermsAddition($storeId = null)
    {
        return $this->getConfigDataFromGeneralGroup('terms-addition', $storeId);
    }

    /**
     * retrieves the for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return string|bool string if the path exists, false otherwise
     */
    public function getTotalMin($storeId = null)
    {
        return $this->getConfigDataFromSolvencyGroup('total-min', $storeId);
    }

    /**
     * retrieves the customer groups the check is skipped for a given store
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return array|bool - array if the path exists, false otherwise
     */
    public function getSkipForCustomerGroups($storeId = null)
    {
        return unserialize(
            $this->getConfigDataFromSolvencyGroup(
                'skip-for-customer-groups', $storeId
            )
        );
    }

    /**
     * retrieves the the default scoring value if the check returns no data
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return int|bool - int if the path exists, false otherwise
     */
    public function getDefaultScoringIfNoDataReturned($storeId = null)
    {
        return $this->getConfigDataFromSolvencyGroup(
            'default-scoring-if-no-data-returned', $storeId
        );
    }

    /**
     * retrieves the the number of checks which are allowed per session
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return int|bool - array if the path exists, false otherwise
     */
    public function getMaxNumberOfChecksPerSession($storeId = null)
    {
        return $this->getConfigDataFromReRequestGroup(
            'max-number-of-checks-per-session', $storeId
        );
    }

    /**
     * retrieves the default scoring value if the count per session is exceeded
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return int|bool - array if the path exists, false otherwise
     */
    public function getDefaultScoringForSessionCount($storeId = null)
    {
        return $this->getConfigDataFromReRequestGroup(
            'default-scoring-for-session-count', $storeId
        );
    }

    /**
     * retrieves the the number of checks which are allowed per day
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return int|bool - array if the path exists, false otherwise
     */
    public function getMaxNumberOfChecksPerDay($storeId = null)
    {
        return $this->getConfigDataFromReRequestGroup(
            'max-number-of-checks-per-day', $storeId
        );
    }

    /**
     * retrieves the default scoring value if the count per day is exceeded
     *
     * @param null $storeId - specifies the store we want to retrieve the value
     *                      for
     *
     * @return int|bool - array if the path exists, false otherwise
     */
    public function getDefaultScoringForDayCount($storeId = null)
    {
        return $this->getConfigDataFromReRequestGroup(
            'default-scoring-for-day-count', $storeId
        );
    }

    /**
     * determines if the scoring value expires after X days
     *
     * @param null $storeId - the store id we want to retrieve the value for it
     *
     * @return bool - true if the setting is set, false otherwise
     */
    public function isScoringExpiringAfterDays($storeId = null)
    {
        return (bool)$this->getConfigDataFromReRequestGroup(
            'scoring-expiring-after-days', $storeId
        );
    }

    /**
     * determines if the scoring check needs to be repeated after X days
     *
     * @param null $storeId - the store id we want to retrieve the value for it
     *
     * @return int|null - the number of days the scoring value needs to be
     *  refreshed, null if not configured yet
     */
    public function getScoringRepeatAfterDays($storeId = null)
    {
        if (false === $this->isScoringExpiringAfterDays($storeId)) {
            return null;
        }
        return $this->getConfigDataFromReRequestGroup(
            'scoring-repeat-after-days', $storeId
        );
    }

    /**
     * retrieves the allowed payment methods for scoring value red
     *
     * @param $storeId
     *
     * @return array
     */
    public function getAllowedPaymentMethodsForScoringValueRed($storeId = null)
    {
        $redValues = unserialize(
            $this->getConfigDataFromScoringValueRedGroup(
                'allowed-payment-methods', $storeId
            )
        );
        if (false === $redValues) {
            $redValues = array();
        }
        $redValues = array_merge(
            $redValues,
            $this->getAlwaysOfferTheFollowingPaymentMethods($storeId)
        );
        return $redValues;
    }

    /**
     * retrieves the allowed payment methods for scoring value yellow
     *
     * @param $storeId
     *
     * @return array
     */
    public function getAllowedPaymentMethodsForScoringValueYellow(
        $storeId = null
    )
    {
        $yellowValues = unserialize(
            $this->getConfigDataFromScoringValueYellowGroup(
                'allowed-payment-methods', $storeId
            )
        );
        if (false === $yellowValues) {
            $yellowValues = array();
        }
        $yellowValues = array_merge(
            $yellowValues,
            $this->getAlwaysOfferTheFollowingPaymentMethods($storeId)
        );
        return $yellowValues;
    }

    /**
     * retrieves the allowed payment methods for scoring value green
     *
     * @param $storeId
     *
     * @return array
     */
    public function getAllowedPaymentMethodsForScoringValueGreen(
        $storeId = null
    )
    {
        $greenValues = unserialize(
            $this->getConfigDataFromScoringValueGreenGroup(
                'allowed-payment-methods', $storeId
            )
        );
        if (false === $greenValues) {
            $greenValues = array();
        }
        $greenValues = array_merge(
            $greenValues,
            $this->getAlwaysOfferTheFollowingPaymentMethods($storeId)
        );
        return $greenValues;
    }

    /**
     * retrieves the additional condition for the green scoring value
     *
     * @param null $storeId
     */
    public function getAdditionalConditionForScoringValueGreen($storeId = null)
    {
        return $this->getConfigDataFromScoringValueGreenGroup(
            'additional-condition', $storeId
        );
    }

    /**
     * retrieves the additional condition for the yellow scoring value
     *
     * @param null $storeId
     */
    public function getAdditionalConditionForScoringValueYellow($storeId = null)
    {
        return $this->getConfigDataFromScoringValueYellowGroup(
            'additional-condition', $storeId
        );
    }

    /**
     * @param int $storeId - the store id we eanto to retrieve the allowed
     *                     countries for
     *
     * @return array - the list of all allowed countries
     */
    public function getAllowedCountries($storeId = null)
    {
        /*
        return unserialize(
            $this->getConfigDataFromGeneralGroup('allowed-country', $storeId)
        );
        */
        return array('DE');
    }

    /**
     * get the epi url from the config.xml
     *
     * @return string
     */
    public function getEpiUrl($storeId = null)
    {
        return $this->getConfigDataFromGeneralGroup('epi-url', $storeId);
    }

    /**
     * checks if logging is enabled or not
     *
     * @param null $storeId
     *
     * @return bool true if logging is enabled, false otherwise
     */
    public function isLoggingEnabled($storeId = null)
    {
        return (bool)$this->getConfigDataFromGeneralGroup(
            'logging-enabled', $storeId
        );
    }

    /**
     * @param int $storeId - the store id for the config
     *
     * @return mixed
     */
    public function getAlwaysOfferTheFollowingPaymentMethods($storeId = null)
    {
        $defaultPaymentMethods = unserialize(
            $this->getConfigDataFromSolvencyGroup(
                'always-offer-the-following-payment-methods', $storeId
            )
        );
        if (false === $defaultPaymentMethods) {
            $defaultPaymentMethods = array();
        }
        return $defaultPaymentMethods;
    }
}