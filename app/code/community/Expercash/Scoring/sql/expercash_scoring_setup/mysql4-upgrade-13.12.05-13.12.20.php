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
    'customer_gender',
    'VARCHAR(1) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_prename',
    'VARCHAR(64) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_name',
    'VARCHAR(64) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_address1',
    'VARCHAR(64) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_address2',
    'VARCHAR(5) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_zip',
    'VARCHAR(10) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_city',
    'VARCHAR(32) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_country',
    'VARCHAR(2) NULL DEFAULT NULL'
);
$conn->addColumn(
    $table,
    'customer_date_of_birth',
    'DATE NULL DEFAULT NULL'
);

$installer->endSetup();