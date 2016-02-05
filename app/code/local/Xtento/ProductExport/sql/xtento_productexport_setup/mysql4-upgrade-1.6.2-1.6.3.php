<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `customer_group_id` INT(10) NOT NULL DEFAULT 0 AFTER `save_files_manual_export`;
");

$installer->endSetup();