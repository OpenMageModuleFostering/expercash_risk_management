<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_ObserverTest
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    public function testFilterPaymentMethods()
    {
        $checkoutSession = $this->getModelMock(
            'checkout/session', array('init', 'save', 'getQuote')
        );
        $checkoutSession->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue(Mage::getModel('sales/quote')));
        $this->replaceByMock('model', 'checkout/session', $checkoutSession);

        $customerSession = $this->getModelMock(
            'customer/session', array('init', 'save')
        );
        $this->replaceByMock('model', 'customer/session', $customerSession);


        $block = new Mage_Payment_Block_Form_Container();
        $block->setMethods(
            array('checkmo' => 'checkmo', 'savedcc' => 'savedcc')
        );

        $configMock = $this->getModelMock(
            'expercash_scoring/config', array('isActive')
        );
        $configMock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));
        $this->replaceByMock('model', 'expercash_scoring/config', $configMock);

        $filterModelMock = $this->getModelMock(
            'expercash_scoring/checkout_payment_methods_filter',
            array('filterPaymentMethods')
        );
        $filterModelMock->expects($this->once())
            ->method('filterPaymentMethods')
            ->will($this->returnValue(array('checkmo' => 'checkmo')));
        $this->replaceByMock(
            'model', 'expercash_scoring/checkout_payment_methods_filter',
            $filterModelMock
        );

        $eventData = new Varien_Object();
        $eventData->setBlock($block);
        $observer = Mage::getModel('expercash_scoring/observer');
        $observer->filterPaymentMethods($eventData);
        $this->assertEquals(1, count($block->getData('methods')));
        $this->assertArrayHasKey('checkmo', $block->getData('methods'));
    }

    public function testSetScoringValueToRegisteredUser()
    {
        $checkoutSession = $this->getModelMock(
            'checkout/session', array('save', 'getQuote')
        );
        $this->replaceByMock('checkout/session', 'model', $checkoutSession);
        Mage::getSingleton('checkout/session')->setData('exp_scoring_id', 1);
        $fakeResult = $this->getModelMock('expercash_scoring/solvency_check_result', array('setCustomerToScoringValue'));
        $observerMock = $this->getModelMock('expercash_scoring/observer', array('getResultModel'));
        $observerMock->expects($this->any())
            ->method('getResultModel')
            ->will($this->returnValue($fakeResult));
        $checkoutSession = $this->getModelMock(
            'checkout/session', array('save', 'getQuote')
        );
        $fakeOrder = new Varien_Object();
        $fakeOrder->setCustomerId(1);
        $event = new Varien_Object();
        $event->setOrder($fakeOrder);
        $observerMock->setScoringValueToRegisteredUser($event);
        $this->assertEquals(null, Mage::getSingleton('checkout/session')->getData('exp_scoring_id'));

    }


    public function testAppendAgreementToBilling()
    {
        $sessionMock = $this->getModelMockBuilder('checkout/session')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);
        $sessionMock = $this->getModelMockBuilder('customer/session')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $this->replaceByMock('singleton', 'customer/session', $sessionMock);
        $transport = new Varien_Object();
        $transport->setHtml('Transport');
        $fakeConfigModel = $this->getModelMock('expercash_scoring/config', array('isActive'));
        $fakeConfigModel->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));
        $observer = $this->getModelMock('expercash_scoring/observer', array('getConfigModel'));
        $observer->expects($this->any())
            ->method('getConfigModel')
            ->will($this->returnValue($fakeConfigModel));
        $event = new Varien_Object();
        $block = Mage::app()->getLayout()->getBlockSingleton('checkout/onepage_billing');
        $blockMock = $this->getBlockMock('expercash_scoring/checkout_onepage_billing_agreement', array('renderView'));
        $blockMock->expects($this->once())
            ->method('renderView')
            ->will($this->returnValue('<b>EXPERCASH</b>'));
        $this->replaceByMock('block', 'expercash_scoring/checkout_onepage_billing_agreement', $blockMock);
        $event->setBlock($block);
        $event->setTransport($transport);
        $observer->appendAgreementToBilling($event);
        $this->assertEquals('Transport<b>EXPERCASH</b>', $transport->getHtml());
        $this->assertNotEquals('<b>EXPERCASH</b>', $transport->getHtml());
    }
}