<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Checkout_Payment_Methods_Filter
{

    /**
     * @return Expercash_Scoring_Model_System_Config_Source_Scoring_Value
     */
    protected function getScoringValueModel()
    {
        return Mage::getModel(
            'expercash_scoring/system_config_source_scoring_value'
        );
    }

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfigModel()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * retrieves the allowed payment methods for given scoring value
     *
     * @param string $scoringValue
     * @param null   $storeId
     *
     * @return array -  empty array if the given scoring value is not valid,
     *                  otherwise an array with the allowed payment methods
     */
    protected function getMethodsForScoringValue($scoringValue, $storeId = null)
    {
        $filteredMethods = array();
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::RED_VALUE
        ) {
            $filteredMethods = $this->getConfigModel()
                ->getAllowedPaymentMethodsForScoringValueRed($storeId);
        }
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::YELLOW_VALUE
        ) {
            $filteredMethods = $this->getConfigModel()
                ->getAllowedPaymentMethodsForScoringValueYellow($storeId);
        }
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::GREEN_VALUE
        ) {
            $filteredMethods = $this->getConfigModel()
                ->getAllowedPaymentMethodsForScoringValueGreen($storeId);
        }
        if ($scoringValue
            === Expercash_Scoring_Model_System_Config_Source_Scoring_Value::NO_TERMS_AND_CONDITIONS_VALUE
        ) {
            $filteredMethods = $this->getConfigModel()
                ->getAlwaysOfferTheFollowingPaymentMethods($storeId);
        }
        return $filteredMethods;
    }

    /**
     * filters the payment methods for a given scoring value
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param array                  $paymentMethods
     * @param                        $scoringValue
     *
     * @return array
     */
    public function filterPaymentMethods(
        Mage_Sales_Model_Quote $quote, array $paymentMethods, $scoringValue
    )
    {
        if (null === $scoringValue
            || !in_array(
                $scoringValue, $this->getScoringValueModel()->validValues()
            )
        ) {
            return $paymentMethods;
        }
        if (0 === count($paymentMethods)) {
            return $paymentMethods;
        }
        $allowPaymentMeths = $this->getMethodsForScoringValue(
            $scoringValue, $quote->getStoreId()
        );

        foreach ($paymentMethods as $key => $method) {
            if (!in_array($method->getCode(), $allowPaymentMeths)) {
                unset($paymentMethods[$key]);
            }
        }
        return $paymentMethods;
    }

} 