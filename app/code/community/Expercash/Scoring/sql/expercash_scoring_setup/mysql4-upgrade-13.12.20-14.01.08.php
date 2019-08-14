<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     ExperCash_Scoring
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
$table = $this->getTable('expercash_scoring/solvency_check_result');
$conn->addColumn(
    $table,
    'escore_value',
    'INT(5) NULL DEFAULT NULL'
);


$installer->endSetup();