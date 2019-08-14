<?php
/**
 * @category   Expercash_Scoring
 * @package    Expercash_Scoring
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co.KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Expercash_Scoring
 *
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co.KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Expercash_Scoring_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE_NAME = 'expercash_scoring.log';

    /**
     * @return Expercash_Scoring_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getModel('expercash_scoring/config');
    }

    /**
     * Returns first not false value of given params.
     *
     * @return mixed
     */
    public function coalesce()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ($arg) {
                return $arg;
            }
        }
        return NULL;
    }

    /**
     * Checks if logging is enabled and if yes, logs given message to logfile
     *
     * @param string $message
     * @param int    $level
     */
    public function log($message, $storeId = null, $level = null)
    {
        if ($this->getConfig()->isLoggingEnabled($storeId)) {
            //Reformat message for better log-visibility
            if (is_array($message)) {
                $message = Mage::helper('core/data')->jsonEncode($message);
            }
            $message = sprintf(
                "\n=====================\n%s\n=====================", $message
            );
            Mage::log($message, $level, self::LOG_FILE_NAME);
        }
    }

    /**
     * checks if user is registering or not
     * @return bool true if users is currently registering false otherwise
     */
    public function isUserRegistering()
    {
        $isRegistering = false;
        $checkoutMethod = Mage::getSingleton('checkout/session')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod === Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
            || $checkoutMethod === Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN
        )
        {
            $isRegistering = true;
        }
        return $isRegistering;

    }
}