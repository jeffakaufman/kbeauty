<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `export_filter_updated_last_x_minutes` int(10) DEFAULT NULL AFTER `export_filter_last_x_days`;
");

$installer->endSetup();