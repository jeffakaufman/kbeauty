<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `export_filter_product_status` varchar(255) NOT NULL DEFAULT '' AFTER `save_files_manual_export`;
");

$installer->endSetup();