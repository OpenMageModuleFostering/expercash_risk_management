<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Solvency_Check_ClientTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testPostRequestSuccessful()
    {
        $response = new Varien_Object();
        $response->setBody('test Response');
        $fakeClient = $this->getMock('Varien_Http_Client', array('request'));
        $fakeClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($response));
        $clientModel = Mage::getModel(
            'expercash_scoring/solvency_check_client'
        );
        $clientModel->setClient($fakeClient);
        $this->assertEquals(
            'test Response', $clientModel->postRequest(array())
        );
    }

    public function testPostRequestRaiseException()
    {
        $response = new Varien_Object();
        $response->setBody('test Response');
        $fakeClient = $this->getMock('Varien_Http_Client', array('request'));
        $fakeClient->expects($this->any())
            ->method('request')
            ->will(
                $this->throwException(new Exception('fake network problem'))
            );
        $clientModel = Mage::getModel(
            'expercash_scoring/solvency_check_client'
        );
        $clientModel->setClient($fakeClient);
        try {
            $clientModel->postRequest(array());
        } catch (Exception $e) {
            $this->assertEquals(
                Mage::helper('expercash_scoring/data')->__(
                    'Gateway request error: %s', 'fake network problem'
                ), $e->getMessage()
            );
        }
    }

    public function testGetClient()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_client');
        $this->assertTrue($model->getClient() instanceof Varien_Http_Client);
    }
}