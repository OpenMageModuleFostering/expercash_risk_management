<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Solvency_Check_Client
{

    /**
     * Max. amount of redirections to follow
     */
    const MAXREDIRECTS = 2;

    /**
     * Timeout in seconds before closing the connection
     */
    const TIMEOUT = 30;

    /**
     * Transport layer for SSL
     */
    const SSLTRANSPORT = 'tcp';

    protected $client = null;

    /**
     * sets the http client
     *
     * @param Varien_Http_Client $client
     */
    public function setClient(Varien_Http_Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Varien_Http_Client $client
     */
    public function getClient()
    {
        if (null == $this->client) {
            $this->client = new Varien_Http_Client();
        }
        return $this->client;
    }

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * configures the client for performing solvency checks
     */
    protected function configureClient()
    {
        $config = $this->getConfig();
        $url = $config->getEpiUrl();
        $client = $this->getClient();
        $client->setUri($url);
        $client->setConfig(
            array(
                 'maxredirects' => self::MAXREDIRECTS,
                 'timeout'      => self::TIMEOUT,
                 'ssltransport' => self::SSLTRANSPORT,
            )
        );
        $this->setClient($client);
    }

    /**
     * retrieves the data helper
     *
     * @return Expercash_Scoring_Helper_Data
     */
    protected function getDataHelper()
    {
        return Mage::helper('expercash_scoring/data');
    }

    /**
     * logs formatted requests / responses
     *
     * @param $params - the requests / responses
     * @param $prefix - additional logging information
     */
    protected function log($params, $prefix)
    {
        if (is_array($params)) {
            $params = Mage::helper('core/data')->jsonEncode($params);
        }
        $message = $prefix . ' ' . $params;
        $this->getDataHelper()->log($message);
    }

    /**
     * performs the post request for the solvency check
     *
     * @param array $params - the params we need to transmit
     *
     * @return string - the response as xml
     */
    public function postRequest(array $params)
    {
        $responseBody = '';
        $this->configureClient();
        $client = $this->getClient();
        $client->setParameterPost($params);
        $client->setMethod(Zend_Http_Client::POST);
        $this->log(
            $params, "Request from Magento to Expercash with following params:"
        );
        try {
            $response = $client->request();
            $responseBody = $response->getBody();
            $this->log($responseBody, 'Gateway gave following response:');
        } catch (Exception $e) {
            Mage::throwException(
                Mage::helper('expercash_scoring/data')->__(
                    'Gateway request error: %s', $e->getMessage()
                )
            );
        }

        return $responseBody;
    }

}