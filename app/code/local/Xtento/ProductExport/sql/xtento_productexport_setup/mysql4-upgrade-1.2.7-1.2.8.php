<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `export_filter_instock_only` INT(1) NOT NULL DEFAULT '0' AFTER `save_files_manual_export`;

ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `export_filter_product_visibility` varchar(255) NOT NULL DEFAULT '' AFTER `save_files_manual_export`;
");

$installer->endSetup();