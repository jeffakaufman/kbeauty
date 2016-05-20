<?php
/**
 *  A Magento module by ProductiveMinds
 *
 * NOTICE OF LICENSE
 *
 * This code is the work and copyright of Productive Minds Ltd, A UK registered company.
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

class Productiveminds_Sitesecurity_Model_Cron
{
	public function mailDeniedAttemps() {
		if (Mage::helper('sitesecurity')->isModuleActive() && Mage::getStoreConfig('sitesecurity_sectns/sitesecurity_grps/email_daily_report')) {
			$items = Mage::getModel('sitesecurity/email')->sendEmail();
		} else {
			Mage::log('Productiveminds_Sitesecurity_Model_Observer::mailDeniedAttemps - denied attempts email is disabled', null, 'Productiveminds_Sitesecurity.log');
		}
	}
	
	public function updateVisitor() {
		if ( Mage::helper('sitesecurity')->isModuleActive() ) {
			Mage::getModel('sitesecurity/cleanhouse')->updateVisitor();
		}
	}
	
	public function cleanHouse() {
		if ( Mage::helper('sitesecurity')->isModuleActive() ) {
			Mage::getModel('sitesecurity/cleanhouse')->doCleanHouse();
		}
	}
	
}