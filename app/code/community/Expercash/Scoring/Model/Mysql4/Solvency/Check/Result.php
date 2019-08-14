<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Mysql4_Solvency_Check_Result
    extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Constructor
     *
     * @see lib/Varien/Varien_Object#_construct()
     * @return Expercash_Scoring_Model_Resource_Solvency_Check_Result
     */
    protected function _construct()
    {
        $this->_init('expercash_scoring/solvency_check_result', 'check_id');
    }
}