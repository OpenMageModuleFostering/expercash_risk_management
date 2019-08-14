<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Mysql4_Solvency_Check_Result_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('expercash_scoring/solvency_check_result');
    }

    /**
     * retrieves the count of the performed checks for the current day
     *
     * @return int the count of the performed checks for the current day
     */
    public function getEntriesForCurrentDay()
    {
        $this->getSelect()->columns('COUNT(check_id) AS cnt_check_id');
        $this->addFieldToFilter(
            new Zend_Db_Expr('DATE(created_at)'),
            array('eq' => new Zend_Db_Expr('CURDATE()'))
        );

        return $this->load()->getFirstItem()->getCntCheckId();
    }

    /**
     * retrieves the last scoring vlaue for an existing customer which
     * was placed during the last x days
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Expercash_Scoring_Model_Config $config
     *
     * @return null - null if no value could be found, the last value otherwise
     */
    public function getLastEntryFromTheLastDays(
        Mage_Sales_Model_Quote $quote, Expercash_Scoring_Model_Config $config
    )
    {
        $scoringValue = null;
        // perform this check only for existing / logged in customer
        if ($quote->getCustomer() && 0 < $quote->getCustomer()->getId()
            && $config->isScoringExpiringAfterDays($quote->getStoreId())
        ) {
            $daysInPast = $config->getScoringRepeatAfterDays(
                $quote->getStoreId()
            );

            // perform the check only if a valid days in past value is found
            if (is_numeric($daysInPast) && 0 < $daysInPast) {
                $expr = sprintf(
                    'DATE_SUB(CURDATE(),INTERVAL %d DAY)', $daysInPast
                );
                $this->addFieldToFilter(
                    'customer_id', array('eq' => $quote->getCustomer()->getId())
                );
                $this->addFieldToFilter(
                    new Zend_Db_Expr('DATE(created_at)'),
                    array('gteq' => new Zend_Db_Expr($expr))
                );
                $this->setOrder('created_at', self::SORT_ORDER_DESC);
                $this->setPageSize(1);
                $scoringValue = $this->load()->getFirstItem()->getEscore();
            }
        }

        return $scoringValue;
    }

    /**
     * deletes entries for a given customer from the scoring result table
     *
     * @param      $customerId - the customer id we want to delete the scoring
     *                          information
     * @param null $entryId     - optional an entry id for a specific result to
     *                          delete
     */
    public function deleteForCustomer($customerId, $entryId = null)
    {
        if (is_numeric($customerId) && 0 < $customerId) {
            $this->addFieldToFilter('customer_id', array('eq' => $customerId));
            if (null !== $entryId && is_numeric($entryId) && 0 < $entryId) {
                $this->addFieldToFilter('check_id', array('eq' => $entryId));
            }
            foreach ($this->load() as $entry) {
                $entry->delete();
            }
        }
    }
}