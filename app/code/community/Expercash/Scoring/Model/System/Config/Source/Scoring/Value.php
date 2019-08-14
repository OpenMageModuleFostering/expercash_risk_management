<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_System_Config_Source_Scoring_Value
{
    const RED_VALUE = 'R';

    const RED_LABEL = 'red';

    const YELLOW_VALUE = 'Y';

    const YELLOW_LABEL = 'yellow';

    const GREEN_VALUE = 'G';

    const GREEN_LABEL = 'green';

    // special internal value if customer has not configmed terms and conditions
    const NO_TERMS_AND_CONDITIONS_VALUE = 'U';

    /**
     * returns all possible values for the scoring value
     *
     * @return array
     */
    public function toOptionArray()
    {
        /* @var $dataHelper Expercash_Scoring_Helper_Data */
        $dataHelper = Mage::helper('expercash_scoring/data');
        $values     = array(
            array(
                'value' => '',
                'label' => $dataHelper->__('-- none --')
            )
        );
        $values[]   = array(
            'value' => self::RED_VALUE,
            'label' => $dataHelper->__(self::RED_LABEL)
        );
        $values[]   = array(
            'value' => self::YELLOW_VALUE,
            'label' => $dataHelper->__(self::YELLOW_LABEL)
        );
        $values[]   = array(
            'value' => self::GREEN_VALUE,
            'label' => $dataHelper->__(self::GREEN_LABEL)
        );

        return $values;
    }

    /**
     * returns an array with all valid values
     *
     * @return array - the valid values
     */
    public function validValues()
    {
        return array(
            self::RED_VALUE, self::YELLOW_VALUE, self::GREEN_VALUE,
            self::NO_TERMS_AND_CONDITIONS_VALUE
        );
    }
}