<?php
/**
 *  A Magento module by ProductiveMinds
 *
 * NOTICE OF LICENSE
 *
 * This code is the effort and copyright of Productive Minds Ltd, A UK registered company.
 * The copyright owner prohibit any fom of distribution of this code
 *
 * DISCLAIMER
 *
 * You are strongly advised to backup ALL your server files and database before installing and/or configuring
 * this Magento module. ProductiveMinds will not take any form of responsibility for any adverse effects that
 * may be cause directly or indirectly by using this software. As a usual practice with Software deployment,
 * the copyright owner recommended that you first install this software on a test server verify its appropriateness
 * before finally deploying it to a live server.
 *
 * @category   	Productiveminds
 * @package    	Productiveminds_Sitesecurity
 * @copyright   Copyright (c) 2010 - 2015 Productive Minds Ltd (http://www.productiveminds.com)
 * @license    	http://www.productiveminds.com/license/license.txt
 * @author     	ProductiveMinds <info@productiveminds.com>
 */

$installer = $this;

$installer->startSetup();

$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/visitor')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/visitor')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`store_id` int(10) unsigned NOT NULL default '0',
	`visitor_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Visitor Id',
	`customer_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Customer ID',
	`last_url_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Last URL ID',
	`first_visit_at` timestamp NULL DEFAULT NULL COMMENT 'First Visit Time',
  	`last_visit_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last Visit Time',
	`session_id` varchar(64) NOT NULL COMMENT 'Session ID',
	`server_addr` bigint(20) DEFAULT NULL COMMENT 'Server Address',
  	`remote_addr` bigint(20) DEFAULT NULL COMMENT 'Remote Address',
	`visitor_country` varchar(4) NOT NULL default '',
	`visitor_type_code` varchar(2) NOT NULL default '',
	`description` text,
	`status` smallint(6) NOT NULL default '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/blacklist')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/blacklist')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`acl_code` varchar(64) NOT NULL COMMENT 'ACL action (e.g goto a cms page)',
	`user_id` int(10) unsigned NOT NULL COMMENT 'Admin User ID',
	`store_id` int(10) unsigned NOT NULL default '0',
	`visitor_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Visitor Id',
	`blacklisted_from` varchar(64) NOT NULL COMMENT 'where this is blacklisted (e.g from visitors list)',
	`server_addr` bigint(20) DEFAULT NULL COMMENT 'Server Address',
	`remote_addr` bigint(20) DEFAULT NULL COMMENT 'Remote Address',
	`description` text,
	`status` smallint(6) NOT NULL default '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/sitesecure')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/sitesecure')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`store_id` int(10) unsigned NOT NULL default '0',
	`country` varchar(24) NOT NULL default '',
	`description` text,
	`status` smallint(6) NOT NULL default '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/country')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/country')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`country` varchar(100) NOT NULL default '',
	`store_id` int(10) unsigned NOT NULL default '0',
	`cat_id` int(10) unsigned NOT NULL default '0',
	`description` text,
	`status` smallint(6) NOT NULL default '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`),
	UNIQUE KEY `country` ( `country`, `store_id` )
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/countrycat')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/countrycat')} (
	`cat_id` int(10) unsigned NOT NULL auto_increment,
	`code` varchar(24) NOT NULL default '',
	`title` varchar(255) NOT NULL default '',
	`description` text,
	PRIMARY KEY (`cat_id`),
	UNIQUE KEY `title` ( `title` )
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/acl')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/acl')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`code` varchar(24) NOT NULL COMMENT 'ACL Type (e.g xssBlock)',
	`description` text,
	`status` smallint(6) NOT NULL default '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
	-- DROP TABLE IF EXISTS `{$installer->getTable('sitesecurity/action')}`;
	CREATE TABLE {$installer->getTable('sitesecurity/action')} (
	`id` int(10) unsigned NOT NULL auto_increment,
	`code` varchar(24) NOT NULL COMMENT 'ACL Type (e.g url)',
	`title` varchar(64) NOT NULL COMMENT 'ACL action (title)',
	`description` text,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$CONTINENT_CODE_EUROPE 		= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_EUROPE;
$CONTINENT_CODE_N_AMERICA 	= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_N_AMERICA;
$CONTINENT_CODE_AFRICA 		= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_AFRICA;
$CONTINENT_CODE_S_AMERICA 	= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_S_AMERICA;
$CONTINENT_CODE_ANTARCTICA 	= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_ANTARCTICA;
$CONTINENT_CODE_ASIA 		= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_ASIA;
$CONTINENT_CODE_OCEANIA 	= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_OCEANIA;
$CONTINENT_CODE_OTHER 	= Productiveminds_Sitesecurity_Model_Security::CONTINENT_CODE_OTHER;

$installer->run("
	INSERT INTO `{$installer->getTable('sitesecurity/countrycat')}` (`code`, `title`, `description`)
	values
	('{$CONTINENT_CODE_EUROPE}', 'Europe', 'European Countries'),
	('{$CONTINENT_CODE_N_AMERICA}', 'North America', 'American Countries'),
	('{$CONTINENT_CODE_AFRICA}', 'Africa', 'African Countries'),
	('{$CONTINENT_CODE_S_AMERICA}', 'South America', 'South American Countries'),
	('{$CONTINENT_CODE_ANTARCTICA}', 'Antarctica', 'Countries in Antarctica'),
	('{$CONTINENT_CODE_ASIA}', 'Asia', 'Asian Countries'),
	('{$CONTINENT_CODE_OCEANIA}', 'Australia/Oceania', 'Australian and Oceanic Countries'),
	('{$CONTINENT_CODE_OTHER}', 'Other', 'A collection for countries whose continent is not unknown');
");

$ACTION_CODE_NONE = Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_NONE;
$ACTION_CODE_BLANK_PAGE = Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_BLANK_PAGE;
$ACTION_CODE_CMS_PAGE = Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CMS_PAGE;
$ACTION_CODE_CUSTOM_URL = Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CUSTOM_URL;

$installer->run("
	INSERT INTO `{$installer->getTable('sitesecurity/action')}` (`code`, `title`, `description`)
	values
	('{$ACTION_CODE_NONE}', 'admin', 'admin'),
	('{$ACTION_CODE_BLANK_PAGE}', 'Go to the blank page', 'Go to the blank page'),
	('{$ACTION_CODE_CMS_PAGE}', 'Go to a CMS page', 'Go to a cms page'),
	('{$ACTION_CODE_CUSTOM_URL}', 'Go to a URL', 'Go to a URL');
");

$ACL_CODE_NONE = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_NONE;
$ACL_CODE_BLACKLIST_COUNTRY = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_COUNTRY;
$ACL_CODE_BLACKLIST_IP = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP;
$ACL_CODE_XSS = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_XSS;
$ACL_CODE_SQL_INJECTION = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_SQL_INJECTION;
$ACL_CODE_EMAIL_INJECTION = Productiveminds_Sitesecurity_Model_Security::ACL_CODE_EMAIL_INJECTION;

$installer->run("
	INSERT INTO `{$installer->getTable('sitesecurity/acl')}` (`code`, `description`, `status`)
	values
	('{$ACL_CODE_NONE}', 'admin', 1),
	('{$ACL_CODE_BLACKLIST_COUNTRY}', 'Blacklisted Country', 1),
	('{$ACL_CODE_BLACKLIST_IP}', 'Blacklisted IP Address', 1),
	('{$ACL_CODE_XSS}', 'Blocked XSS Threat', 1),
	('{$ACL_CODE_SQL_INJECTION}', 'Blocked SQL Injection', 1),
	('{$ACL_CODE_EMAIL_INJECTION}', 'Blocked Email Injection', 1);
");

$installer->run("
	ALTER TABLE {$this->getTable('sales_flat_order')} ADD `pms_sitesecurity_ip` bigint(20) DEFAULT NULL COMMENT 'Customer IP Address';
");

$installer->run("
	ALTER TABLE {$this->getTable('review/review_detail')} ADD `pms_sitesecurity_ip` bigint(20) DEFAULT NULL COMMENT 'Customer IP Address';
");

$installer->endSetup();



// create a blank cms page
$identifier = Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_IDENTIFIER;
$newPageModel = Mage::getModel('cms/page')->load($identifier, 'identifier');
if (!$newPageModel->getPageId()) {
	Mage::getModel('sitesecurity/cms_page')->createNewPage();
}

