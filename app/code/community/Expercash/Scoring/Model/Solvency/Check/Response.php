<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Solvency_Check_Response
{

    /**
     * parses the expercash response
     *
     * @param $xmlString - xml response as string
     *
     * @return array - the parsed response containing the neccessary data
     */
    public function parseResponse($xmlString)
    {
        libxml_use_internal_errors(true);
        $simpleXMLResponse = simplexml_load_string($xmlString);

        // raise exception if no xml is given
        if (false === $simpleXMLResponse
            || true === is_null($simpleXMLResponse)
            || !$simpleXMLResponse instanceof SimpleXMLElement
        )
            Mage::throwException(
                Mage::helper('expercash_scoring/data')->__(
                    'Error while transforming response to simple xml.'
                )
            );
        $responseArray = Mage::helper('core/data')->xmlToAssoc(
            $simpleXMLResponse
        );

        // raise exception if no xml in the expercash format is given
        if (!array_key_exists('rc', $responseArray)
            || $responseArray['rc'] != 100
        )
            Mage::throwException(
                Mage::helper('expercash_scoring/data')->__(
                    'Invalid response due to technical problem!'
                )
            );

        // remove the unnecessary data from the response
        if (array_key_exists('escore_scoring', $responseArray)) {
            unset($responseArray['escore_scoring']);
        }
        return $responseArray;
    }
}