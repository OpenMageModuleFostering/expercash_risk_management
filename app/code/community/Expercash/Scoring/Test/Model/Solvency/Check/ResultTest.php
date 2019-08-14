<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Model_Solvency_Check_ResultTest
    extends EcomDev_PHPUnit_Test_Case
{

    public function testSetScoringData()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setId(1);
        $model = Mage::getModel('expercash_scoring/solvency_check_result');
        $model->setScoringData($quote, array());
        $this->assertEquals($quote->getId(), $model->getQuoteId());
        $this->assertEquals(null, $model->getCustomerId());

        $fakeCustomer = Mage::getModel('customer/customer');
        $fakeCustomer->setId(1);
        $quote->setCustomer($fakeCustomer);

        $model->setScoringData($quote, array());
        $this->assertEquals($quote->getId(), $model->getQuoteId());
        $this->assertEquals($fakeCustomer->getId(), $model->getCustomerId());
        $this->assertEquals(null, $model->getEscore());

        $model->setScoringData($quote, array('escore' => 'Y'));
        $this->assertEquals($quote->getId(), $model->getQuoteId());
        $this->assertEquals($fakeCustomer->getId(), $model->getCustomerId());
        $this->assertEquals(null, $model->getEscoreFeature());
        $this->assertEquals('Y', $model->getEscore());

        $model->setScoringData(
            $quote, array('escore' => 'Y', 'escore_feature' => 'PAB')
        );
        $this->assertEquals($quote->getId(), $model->getQuoteId());
        $this->assertEquals($fakeCustomer->getId(), $model->getCustomerId());
        $this->assertEquals('PAB', $model->getEscoreFeature());
        $this->assertEquals('Y', $model->getEscore());

        $model->setScoringData(
            $quote, array(
                         'escore'       => 'Y', 'escore_feature' => 'PAB',
                         'escore_value' => 3.0
                    )
        );
        $this->assertEquals($quote->getId(), $model->getQuoteId());
        $this->assertEquals($fakeCustomer->getId(), $model->getCustomerId());
        $this->assertEquals('PAB', $model->getEscoreFeature());
        $this->assertEquals('Y', $model->getEscore());
        $this->assertEquals(3.0, $model->getEscoreValue());
    }


    public function testSetAddressData()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result');
        $model->setAddressData(array());
        $this->assertEquals(null, $model->getCustomerAddress1());
        $streetTooLong
            = '1234567890123456789012345678901234567890123456789012345678901234567890';
        $model->setAddressData(array('customer_address1' => $streetTooLong));
        $this->assertEquals(null, $model->getCustomerAddress1());
        $model->setAddressData(array('customer_address1' => 'Nonnenstr.'));
        $this->assertEquals('Nonnenstr.', $model->getCustomerAddress1());
        $this->assertEquals(null, $model->getCustomerAddress2());

        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '1234567'
            )
        );
        $this->assertEquals('Nonnenstr.', $model->getCustomerAddress1());
        $this->assertEquals(null, $model->getCustomerAddress2());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d'
            )
        );
        $this->assertEquals('11d', $model->getCustomerAddress2());
        $this->assertEquals(null, $model->getCustomerZip());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '12345678901'
            )
        );
        $this->assertEquals('11d', $model->getCustomerAddress2());
        $this->assertEquals(null, $model->getCustomerZip());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '04229'
            )
        );
        $this->assertEquals('04229', $model->getCustomerZip());
        $this->assertEquals(null, $model->getCustomerCity());
        $cityNameTooLong = '0123456789012345678901234567891234';
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '04229',
                 'customer_city'     => $cityNameTooLong
            )
        );
        $this->assertEquals('04229', $model->getCustomerZip());
        $this->assertEquals(null, $model->getCustomerCity());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '04229',
                 'customer_city'     => 'Leipzig'
            )
        );
        $this->assertEquals('Leipzig', $model->getCustomerCity());
        $this->assertEquals(null, $model->getCustomerCountry());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '04229',
                 'customer_city'     => 'Leipzig', 'customer_country' => '123'
            )
        );
        $this->assertEquals('Leipzig', $model->getCustomerCity());
        $this->assertEquals(null, $model->getCustomerCountry());
        $model->setAddressData(
            array(
                 'customer_address1' => 'Nonnenstr.',
                 'customer_address2' => '11d', 'customer_zip' => '04229',
                 'customer_city'     => 'Leipzig', 'customer_country' => 'DE'
            )
        );
        $this->assertEquals('Leipzig', $model->getCustomerCity());
        $this->assertEquals('DE', $model->getCustomerCountry());

    }

    public function testSetCustomerData()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result');
        $model->setCustomerData(array());
        $this->assertEquals(null, $model->getCustomerGender());
        $model->setCustomerData(array('customer_gender' => 'mm'));
        $this->assertEquals(null, $model->getCustomerGender());
        $model->setCustomerData(array('customer_gender' => 'm'));
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals(null, $model->getCustomerPrename());
        $nameTooLong
            = '012345678901234567890123456789012345678901234567890123456789123456';
        $model->setCustomerData(
            array('customer_gender' => 'm', 'customer_prename' => $nameTooLong)
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals(null, $model->getCustomerPrename());
        $model->setCustomerData(
            array('customer_gender' => 'm', 'customer_prename' => 'Max')
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $model->setCustomerData(
            array('customer_gender' => 'm', 'customer_name' => $nameTooLong)
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $this->assertEquals(null, $model->getCustomerName());
        $model->setCustomerData(
            array('customer_gender' => 'm', 'customer_name' => 'Power')
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $this->assertEquals('Power', $model->getCustomerName());
        $this->assertEquals(null, $model->getCustomerDateOfBirth());
        $model->setCustomerData(
            array(
                 'customer_gender'        => 'm', 'customer_name' => 'Power',
                 'customer_date_of_birth' => 'abcdef'
            )
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $this->assertEquals('Power', $model->getCustomerName());
        $this->assertEquals(null, $model->getCustomerDateOfBirth());
        $model->setCustomerData(
            array(
                 'customer_gender'        => 'm', 'customer_name' => 'Power',
                 'customer_date_of_birth' => '123'
            )
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $this->assertEquals('Power', $model->getCustomerName());
        $this->assertEquals(null, $model->getCustomerDateOfBirth());
        $model->setCustomerData(
            array(
                 'customer_gender'        => 'm', 'customer_name' => 'Power',
                 'customer_date_of_birth' => '19900101'
            )
        );
        $this->assertEquals('m', $model->getCustomerGender());
        $this->assertEquals('Max', $model->getCustomerPrename());
        $this->assertEquals('Power', $model->getCustomerName());
        $this->assertEquals('1990-01-01', $model->getCustomerDateOfBirth());

    }


    /**
     * @loadFixture solvency_check_result
     */
    public function testSetCustomerIdToScoringValue()
    {
        $model = Mage::getModel('expercash_scoring/solvency_check_result');
        $model->setCustomerToScoringValue(null, null);
        $model->load(null);
        $this->assertEquals(null, $model->getCustomerId());

        $model->setCustomerToScoringValue(0, null);
        $model->load(0);
        $this->assertEquals(null, $model->getCustomerId());

        $model->setCustomerToScoringValue(1, null);
        $model->load(1);
        $this->assertEquals(null, $model->getCustomerId());

        $model->setCustomerToScoringValue(1, 1);
        $model->load(1);
        $this->assertEquals(1, $model->getCustomerId());


    }
} 