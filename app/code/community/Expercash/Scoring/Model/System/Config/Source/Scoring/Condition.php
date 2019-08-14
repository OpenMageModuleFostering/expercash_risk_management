<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_System_Config_Source_Scoring_Condition
{

    const PERSON_KNOWN_VALUE = 'PPB';

    const PERSON_KNOWN_LABEL = 'Person known';

    const DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE = 'PHB';

    const DOMESTIC_HOME_OR_PERSON_KNOWN_LABEL = 'Domestic home or person known';

    const PERSON_KNOWN_UNDELIVERABLE_VALUE = 'PNZ';

    const PERSON_KNOWN_UNDELIVERABLE_LABEL = 'Person known, but undeliverable';

    const ADDRESS_KNOWN_NO_PERSONAL_INFO_VALUE = 'PAB';

    const ADDRESS_KNOWN_NO_PERSONAL_INFO_LABEL = 'Address known, but no personal information';

    const NO_POSTAL_INFORMATION_VALUE = 'PKI';

    const NO_POSTAL_INFORMATION_LABEL = 'No postal information given';

    const PERSON_KNOWN_BUT_DEAD_VALUE = 'PPV';

    const PERSON_KNOWN_BUT_DEAD_LABEL = 'Person known, but dead';

    const ADDRESS_WRONG_VALUE = 'PPF';

    const ADDRESS_WRONG_LABEL = 'Address wrong';

    /**
     * returns all additional conditions for the scoring values yellow and green
     *
     * @return array
     */
    public function toOptionArray()
    {
        /* @var $dataHelper Expercash_Scoring_Helper_Data */
        $dataHelper = Mage::helper('expercash_scoring/data');
        $values = array(array(
                            'value' => '',
                            'label' => $dataHelper->__('-- none --')
                        ));
        $values[] = array(
            'value' => self::PERSON_KNOWN_VALUE,
            'label' => $dataHelper->__(self::PERSON_KNOWN_LABEL)
        );
        $values[] = array(
            'value' => self::DOMESTIC_HOME_OR_PERSON_KNOWN_VALUE,
            'label' => $dataHelper->__(self::DOMESTIC_HOME_OR_PERSON_KNOWN_LABEL)
        );

        return $values;
    }

}