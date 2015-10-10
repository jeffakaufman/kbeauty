<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `attributes_to_select` text NOT NULL DEFAULT '' AFTER `output_type`;
");

$installer->endSetup();