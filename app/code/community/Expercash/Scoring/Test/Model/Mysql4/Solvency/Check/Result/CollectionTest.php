<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Expercash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Mysql4_Solvency_Check_Result_CollectionTest
    extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     * simulates guest checkout
     */
    public function testGetLastEntryFromTheLastDaysDefault()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $quote = Mage::getModel('sales/quote');
        $config = Mage::getModel('expercash_scoring/config');
        $this->assertEquals(
            null, $model->getLastEntryFromTheLastDays($quote, $config),
            'default value for the last scoring value is null'
        );
    }

    public function testGetLastEntryFromTheLastDaysDefaultDueToMissingDays()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $quote = Mage::getModel('sales/quote');
        $customer = Mage::getModel('customer/customer');
        $customer->setId(1);
        $quote->setCustomer($customer);
        $config = $this->getModelMock(
            'expercash_scoring/config',
            array('isScoringExpiringAfterDays', 'getScoringRepeatAfterDays')
        );
        $config->expects($this->any())
            ->method('isScoringExpiringAfterDays')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getScoringRepeatAfterDays')
            ->will($this->returnValue('not numeric'));
        $this->assertEquals(
            null, $model->getLastEntryFromTheLastDays(
                $quote, $config, 'not numeric days should lead to null value'
            )
        );
    }

    public function testGetLastEntryFromTheLastDaysWithResult()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(1);
        $quote->save();
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setId(1);
        $customer->setEmail('a@b.com');
        $customer->save();
        $now = new DateTime();
        $checkResult = Mage::getModel(
            'expercash_scoring/solvency_check_result'
        );
        $checkResult->setCheckResultId(1);
        $checkResult->setQuoteId(1);
        $checkResult->setCustomerId(1);
        $checkResult->setEscore('Y');
        $checkResult->setCreatedAt(
            $now->sub(new DateInterval('P5D'))->format('Y-m-d')
        );
        $checkResult->save();
        $now = new DateTime();
        $anotherResult = Mage::getModel(
            'expercash_scoring/solvency_check_result'
        );
        $anotherResult->setCheckResultId(2);
        $anotherResult->setQuoteId(1);
        $anotherResult->setCustomerId(1);
        $anotherResult->setEscore('G');
        $anotherResult->setCreatedAt(
            $now->sub(new DateInterval('P3D'))->format('Y-m-d')
        );
        $anotherResult->save();
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $quote = Mage::getModel('sales/quote');

        $quote->setCustomer($customer);
        $config = $this->getModelMock(
            'expercash_scoring/config',
            array('isScoringExpiringAfterDays', 'getScoringRepeatAfterDays')
        );
        $config->expects($this->any())
            ->method('isScoringExpiringAfterDays')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getScoringRepeatAfterDays')
            ->will($this->returnValue(6));
        $this->assertEquals(
            'G', $model->getLastEntryFromTheLastDays(
                $quote, $config, 'not numeric days should lead to null value'
            )
        );
        $checkResult->delete();
        $anotherResult->delete();
    }

    public function testGetLastEntryFromTheLastDaysWithNoResult()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(1);
        $quote->save();
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setId(1);
        $customer->setEmail('a@b.com');
        $customer->save();
        $now = new DateTime();
        $checkResult = Mage::getModel(
            'expercash_scoring/solvency_check_result'
        );
        $checkResult->setCheckResultId(1);
        $checkResult->setQuoteId(1);
        $checkResult->setCustomerId(1);
        $checkResult->setEscore('Y');
        $checkResult->setCreatedAt(
            $now->sub(new DateInterval('P5D'))->format('Y-m-d')
        );
        $checkResult->save();
        $now = new DateTime();
        $anotherResult = Mage::getModel(
            'expercash_scoring/solvency_check_result'
        );
        $anotherResult->setCheckResultId(2);
        $anotherResult->setQuoteId(1);
        $anotherResult->setCustomerId(1);
        $anotherResult->setEscore('G');
        $anotherResult->setCreatedAt(
            $now->sub(new DateInterval('P3D'))->format('Y-m-d')
        );
        $anotherResult->save();
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $quote = Mage::getModel('sales/quote');

        $quote->setCustomer($customer);
        $config = $this->getModelMock(
            'expercash_scoring/config',
            array('isScoringExpiringAfterDays', 'getScoringRepeatAfterDays')
        );
        $config->expects($this->any())
            ->method('isScoringExpiringAfterDays')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getScoringRepeatAfterDays')
            ->will($this->returnValue(1));
        $this->assertEquals(
            null, $model->getLastEntryFromTheLastDays(
                $quote, $config, 'not numeric days should lead to null value'
            )
        );
        $checkResult->delete();
        $anotherResult->delete();
        $quote->delete();
    }

    public function testGetEntriesForCurrentDayWithoutEntries()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $this->assertEquals(0, $model->getEntriesForCurrentDay());
    }

    public function testGetEntriesForCurrentDay()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(1);
        $quote->save();
        $now = new DateTime();
        $checkResult = Mage::getModel(
            'expercash_scoring/solvency_check_result'
        );
        $checkResult->setCheckResultId(1);
        $checkResult->setQuoteId(1);
        $checkResult->setEscore('R');
        $checkResult->setCreatedAt(
            $now->format('Y-m-d')
        );
        $checkResult->save();
        $model = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection();
        $this->assertEquals(1, $model->getEntriesForCurrentDay());
        $checkResult->delete();
        $quote->delete();
    }

    /**
     * @loadFixture checks.yaml
     */
    public function testDelete()
    {
        $collection = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection()->load();
        $oldCollectionCount = $collection->count();
        Mage::getModel('expercash_scoring/solvency_check_result')
            ->deleteForCustomer(2);
        $collection = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection()->load();
        $this->assertEquals($oldCollectionCount, $collection->count());

        Mage::getModel('expercash_scoring/solvency_check_result')
            ->deleteForCustomer(1, 1);
        $collection = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection()->load();
        $this->assertEquals($oldCollectionCount - 1, $collection->count());

        Mage::getModel('expercash_scoring/solvency_check_result')
            ->deleteForCustomer(1);
        $collection = Mage::getModel('expercash_scoring/solvency_check_result')
            ->getCollection()->load();
        $this->assertEquals(0, $collection->count());
    }

} 