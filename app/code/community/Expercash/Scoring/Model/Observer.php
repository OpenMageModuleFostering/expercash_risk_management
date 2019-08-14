<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Model_Observer
{

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
     * Append the solvency confirmation checkbox to billing address
     *
     * @param  $observer
     *
     * @return void
     */
    public function appendAgreementToBilling($observer)
    {
        if (
            $observer->getBlock() instanceof Mage_Checkout_Block_Onepage_Billing
            && false === $observer->getBlock() instanceof
            Mage_Paypal_Block_Express_Review_Billing
        ) {

            $transport   = $observer->getTransport();
            $block       = $observer->getBlock();
            $quote       = $block->getQuote();
            $configModel = $this->getConfigModel();
            if ($this->getConfigModel()->isActive(
                $quote->getStoreId()
            )
                && null === $this->getResultModel()->getCollection()
                    ->getLastEntryFromTheLastDays($quote, $configModel)
            ) {
                $layout            = $block->getLayout();
                $html              = $transport->getHtml();
                $solvencyCheckHtml = $layout->createBlock(
                    'expercash_scoring/checkout_onepage_billing_agreement',
                    'onepage_scoring_agreement'
                )
                    ->setTemplate('expercash-scoring/scoring/agreement.phtml')
                    ->renderView();
                $html              = $html . $solvencyCheckHtml;
                $transport->setHtml($html);
            }
        }
    }

    /**
     * checks if the terms and conditions were confirmed or not.
     * If not the error message is appended to the error messages stack
     *
     * @param $observer
     *
     * @return $this
     */
    public function performSolvencyCheck($observer)
    {
        /** @var $controller Mage_Checkout_Controller_Action */
        $controller    = $observer->getControllerAction();
        $request       = $controller->getRequest();
        $quote         = Mage::getSingleton('checkout/session')->getQuote();
        $scoringResult = null;
        // check if terms and conditions are confirmed
        if ($controller instanceof Mage_Checkout_Controller_Action
            && 'saveBilling' === $request->getActionName()
            && $this->getConfigModel()->isActive(
                $quote->getStoreId()
            )
        ) {
            // in case there were errors before, don't perform solvency check
            $prevErrorMessages = Mage::helper('core')->jsonDecode(
                $controller->getResponse()->getBody()
            );
            if (array_key_exists('error', $prevErrorMessages)) {
                return $this;
            }
            $scoringResult = Mage::getModel('expercash_scoring/solvency_check')
                ->performSolvencyCheck(
                    $quote, $request
                );
            Mage::getSingleton('checkout/session')->setData(
                'ExperCash_Scoring_Check_Value', $scoringResult
            );

        }
    }

    /**
     * triggers the filtering of the payment methods
     *
     * @param $observer
     */
    public function filterPaymentMethods($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Payment_Block_Form_Container) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if ($this->getConfigModel()->isActive(
                $quote->getStoreId()
            )
            ) {
                $methods = $block->getMethods();
                /** @var $filterModel Expercash_Scoring_Model_Checkout_Payment_Methods_Filter */
                $filterModel  = Mage::getModel(
                    'expercash_scoring/checkout_payment_methods_filter'
                );
                $scoringValue = Mage::getSingleton('checkout/session')->getData(
                    'ExperCash_Scoring_Check_Value'
                );
                $methods      = $filterModel->filterPaymentMethods(
                    $quote, $methods, $scoringValue
                );
                $block->setData('methods', $methods);
            }
        }
    }

    /**
     * perform update for scoring value entry if needed (= user was registering)
     *
     * @param $observer
     */
    public function setScoringValueToRegisteredUser($observer)
    {
        $order = $observer->getOrder();
        $session = Mage::getSingleton('checkout/session');
        $scoringValueId = $session->getData('exp_scoring_id');
        $this->getResultModel()->setCustomerToScoringValue($scoringValueId, $order->getCustomerId());
        $session->unsetData('exp_scoring_id');
    }

}