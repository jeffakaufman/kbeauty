<?php

$installer = $this;

$installer->startSetup();

$installer->run("
		-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/sitesecure')}`;
		CREATE TABLE {$installer->getTable('sitesecurity/sitesecure')} (
		`id` int(10) unsigned NOT NULL auto_increment,
		`type_code` varchar(24) NOT NULL COMMENT 'ACL Type (e.g xssBlock)',
		`store_id` int(10) unsigned NOT NULL default '0',
		`customer_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Customer ID',
		`url` varchar(255) NOT NULL COMMENT 'Visited URL',
		`server_addr` bigint(20) DEFAULT NULL COMMENT 'Server Address',
	  	`remote_addr` bigint(20) DEFAULT NULL COMMENT 'Remote Address',
		`visitor_country` varchar(4) NOT NULL default '',
		`visitor_type_code` varchar(2) NOT NULL default '',
		`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$ACL_CODE_FORM_INJECTION = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_FORM_INJECTION;
$installer->run("
	INSERT INTO `{$installer->getTable('sitesecurity/acl')}` (`code`, `description`, `status`)
	values
	('{$ACL_CODE_FORM_INJECTION}', 'Block a Form Injection', 1);
");

$installer->endSetup();
