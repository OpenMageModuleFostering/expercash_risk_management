<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Adminhtml_ScoringController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(
            'customer/manage'
        );
    }

    /**
     * action for the scoring values grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->initCustomer('id');
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'expercash_scoring/adminhtml_customer_edit_tab_expercash_scoring'
            )
                ->toHtml()
        );
    }

    /**
     * deletes entries for a given customer
     */
    public function deleteAction()
    {
        $customer   = $this->initCustomer('customer_id');
        $customerId = $customer->getId();
        $entryId    = $this->getRequest()->getParam('check_id');
        if (0 < $customerId) {
            Mage::getModel('expercash_scoring/solvency_check_result')
                ->deleteForCustomer($customerId, $entryId);
        }
        $this->_redirect(
            'adminhtml/customer/edit', array(
                                            'id'   => $customerId,
                                            'back' => 'edit',
                                            'tab'  => 'customer_edit_tab_expercash_scoring',
                                       )
        );
    }

    /**
     * inits and registers the customer
     *
     * @param $paramKey - the key which holds the id of the customer in the params
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function initCustomer($paramKey)
    {
        $customerId = $this->getRequest()->getParam($paramKey);
        $customer   = Mage::getModel('customer/customer')->load($customerId);
        Mage::register('current_customer', $customer, true);

        return $customer;
    }
} 