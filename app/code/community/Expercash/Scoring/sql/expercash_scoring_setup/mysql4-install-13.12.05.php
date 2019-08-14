<?php
/**
 * @category   Expercash_Scoring
 * @package    Expercash_Scoring
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co.KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Expercash_Scoring_Sql_Setup
 *
 * @author     Michael Lühr <michael.leuhr@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co.KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* @var $installer Expercash_Scoring_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$installer->run(
    "
    DROP TABLE IF EXISTS {$installer->getTable('expercash_scoring/solvency_check_result')};
    CREATE TABLE {$installer->getTable('expercash_scoring/solvency_check_result')} (
        `check_id` int(10) unsigned NOT NULL auto_increment,
        `quote_id` int(10) unsigned,
        `customer_id` int(10) unsigned,
        `escore` varchar(10),
        `escore_feature` varchar(10),
        `created_at` timestamp default CURRENT_TIMESTAMP,
        PRIMARY KEY (`check_id`),
        FOREIGN KEY (`customer_id`) REFERENCES customer_entity(entity_id)
                      ON DELETE CASCADE
                      ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);


$installer->endSetup();