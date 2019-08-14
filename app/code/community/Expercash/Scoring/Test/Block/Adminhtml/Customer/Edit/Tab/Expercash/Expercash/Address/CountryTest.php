<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Expercash_Scoring_Test_Block_Adminhtml_Customer_Edit_Tab_Expercash_Expercash_Address_CountryTest
    extends EcomDev_PHPUnit_Test_Case
{


    protected $renderer;

    public function setUp()
    {
        $this->renderer
            = new Expercash_Scoring_Block_Adminhtml_Customer_Edit_Tab_Expercash_Renderer_Address_Country();
        $column = new Varien_Object();
        $column->setIndex('customer_country');
        $this->renderer->setColumn($column);
    }

    public function testRender()
    {
        $row = new Varien_Object();
        $this->assertEquals('', $this->renderer->render($row));
        $row->setCustomerCountry('DE');
        $this->assertEquals('Germany', $this->renderer->render($row));
        $row->setCustomerCountry('XY');
        $this->assertEquals('', $this->renderer->render($row));
    }
} 