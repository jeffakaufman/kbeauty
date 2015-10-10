<?php
/**
 * Remove or Change Displayed States and Regions
 *
 * LICENSE
 *
 * This source file is subject to the Eltrino LLC EULA
 * that is bundled with this package in the file LICENSE_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://eltrino.com/license-eula.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Region
 * @copyright   Copyright (c) 2014 Eltrino LLC. (http://eltrino.com)
 * @license     http://eltrino.com/license-eula.txt  Eltrino LLC EULA
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run(
    "
CREATE TABLE IF NOT EXISTS `{$installer->getTable('eltrino_region/entity')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Region Entity Id',
  `country_id` varchar(2) DEFAULT NULL COMMENT 'Country Id',
  `region_id` int(10) unsigned NOT NULL COMMENT 'Region Id',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Region limitations';
"
);

$installer->endSetup();
