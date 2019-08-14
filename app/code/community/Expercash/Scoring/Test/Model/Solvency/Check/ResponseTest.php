<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Solvency_Check_ResponseTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected function getXml($fileName)
    {
        $fileName
            = __DIR__ . DS . 'ResponseTest' . DS . 'TestFiles' . DS . $fileName;
        if (!file_exists($fileName)) {
            throw new Exception($fileName . ' does not exist!');
        }
        if (!is_readable($fileName)) {
            throw new Exception($fileName . ' is not readable!');
        }

        return file_get_contents($fileName);
    }

    public function testParseResponseThrowsException()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        try {
            $model->parseResponse('');
        } catch (Exception $e) {
            $message = Mage::helper('expercash_scoring/data')->__(
                'Error while transforming response to simple xml.'
            );
            $this->assertEquals($message, $e->getMessage());
        }
        try {
            $model->parseResponse('kein xml');
        } catch (Exception $e) {
            $message = Mage::helper('expercash_scoring/data')->__(
                'Error while transforming response to simple xml.'
            );
            $this->assertEquals($message, $e->getMessage());
        }
        try {
            $model->parseResponse("<foo>auch kein xml</foo>");
        } catch (Exception $e) {
            $message = Mage::helper('expercash_scoring/data')->__(
                'Invalid response due to technical problem!'
            );
            $this->assertEquals($message, $e->getMessage());
        }
    }

    public function testParseResponseFailsDueToInvalidResponse()
    {
        $xml = $this->getXml('InvalidResponse.xml');
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        try {
            $model->parseResponse($xml);
        } catch (Exception $e) {
            $this->assertEquals(
                Mage::helper('expercash_scoring/data')->__(
                    'Invalid response due to technical problem!'
                ), $e->getMessage()
            );
        }
    }

    public function testParseResponseSuccessful()
    {
        $xml = $this->getXml('CompleteSuccessfulResponse.xml');
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        $result = $model->parseResponse($xml);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('escore', $result);
        $this->assertArrayHasKey('escore_feature', $result);
    }

    public function testParseResponseSuccessfulButMissingEscore()
    {
        $xml = $this->getXml('SuccessfulResponseMissingEscore.xml');
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        $result = $model->parseResponse($xml);
        $this->assertTrue(is_array($result));
        $this->assertArrayNotHasKey('escore', $result);
        $this->assertArrayHasKey('escore_feature', $result);
    }

    public function testParseResponseSuccessfulButMissingEscoreFeature()
    {
        $xml = $this->getXml('SuccessfulResponseMissingEscoreFeature.xml');
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        $result = $model->parseResponse($xml);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('escore', $result);
        $this->assertArrayNotHasKey('escore_feature', $result);
    }
    public function testParseResponseSuccessfulRemoveEscoreScoring()
    {
        $xml = $this->getXml('SuccessfulResponseEscoreScoring.xml');
        $model = Mage::getModel('expercash_scoring/solvency_check_response');
        $result = $model->parseResponse($xml);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('escore', $result);
        $this->assertArrayNotHasKey('escore_scoring', $result);
    }
}