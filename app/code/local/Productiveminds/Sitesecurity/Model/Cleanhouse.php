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

class Productiveminds_Sitesecurity_Model_Cleanhouse extends Mage_Core_Model_Abstract
{
	const VISITOR_TYPE_CUSTOMER = 'c';
	const VISITOR_TYPE_VISITOR  = 'v';
	
	public function updateVisitor() {
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$collection = Mage::getModel('sitesecurity/visitor')->getCollection()
		->setOrder('visitor_id', 'DESC');
		
		$lastRecordedVisitorId = 0;
		if(!empty($collection) && null != $collection && count($collection) > 1) {
			$lastRecordedVisitorId = $collection->getFirstItem()->getVisitorId();
		}
		
		// do it anyway, for the first time but for only the last visitor
		if ($lastRecordedVisitorId == 0 ) {
			$logCollection = Mage::getModel('log/visitor')->getCollection()
			->setOrder('visitor_id', 'DESC')
			->setPageSize(1)
			->setCurPage(1);
			$lastRecordedVisitorId = (int)$logCollection->getFirstItem()->getVisitorId();
			if($lastRecordedVisitorId > 1000) {
				$lastRecordedVisitorId = $lastRecordedVisitorId-1000;
			} else {
				$lastRecordedVisitorId = $lastRecordedVisitorId-1;
			}
		}
		
		$log_visitor_table = Mage::getSingleton('core/resource')->getTableName('log/visitor');
		$log_visitor_info_table = Mage::getSingleton('core/resource')->getTableName('log/visitor_info');
		$log_customer_table = Mage::getSingleton('core/resource')->getTableName('log/customer');
		$sitesecure_visitor_table = Mage::getSingleton('core/resource')->getTableName('sitesecurity/visitor');	
		
		$sql = "SELECT lvt.*, lvit.server_addr, lvit.remote_addr, cust.customer_id
		FROM `{$log_visitor_table}` AS `lvt`
		JOIN `{$log_visitor_info_table}` AS `lvit`
		ON lvit.visitor_id = lvt.visitor_id
		LEFT JOIN `{$log_customer_table}` AS `cust`
		ON cust.visitor_id = lvt.visitor_id
		WHERE lvt.visitor_id > '{$lastRecordedVisitorId}'";
		
		$outStandingVisitors = $read->fetchAll($sql);
		
		foreach ($outStandingVisitors as $aVisitor) {
			// set if visitor is a registered customer or a casual visitor
			if($aVisitor['customer_id'] == null || $aVisitor['customer_id'] == '') {
				$visitor_type_code = self::VISITOR_TYPE_VISITOR;
			} else {
				$visitor_type_code = self::VISITOR_TYPE_CUSTOMER;
			}
			// set visitor country
			// Magento 1.9 uses @inet_ntop
			if ( version_compare( Mage::getVersion(), '1.9.0.0', 'ge') ) {
				$server_ipAddy = @inet_ntop($aVisitor['server_addr']);
				$ipAddy = @inet_ntop($aVisitor['remote_addr']);
			} else {
				$server_ipAddy = long2ip($aVisitor['server_addr']);
				$ipAddy = long2ip($aVisitor['remote_addr']);
			}
			$server_ipAddySystemable = Mage::getModel('sitesecurity/security')->getIp2long($server_ipAddy);
			$ipAddySystemable = Mage::getModel('sitesecurity/security')->getIp2long($ipAddy);
			
			$visitor_country = Mage::helper('sitesecurity/sitesecurity')->getVisitorCountry($ipAddySystemable);
			
			$sql = "INSERT INTO `{$sitesecure_visitor_table}`
			(visitor_id, store_id, customer_id, last_url_id, session_id, server_addr, remote_addr, visitor_country, visitor_type_code, first_visit_at, last_visit_at) values
			(
			'{$aVisitor['visitor_id']}',
			'{$aVisitor['store_id']}',
			'{$aVisitor['customer_id']}',
			'{$aVisitor['last_url_id']}',
			'{$aVisitor['session_id']}',
			'{$server_ipAddySystemable}',
			'{$ipAddySystemable}',
			'{$visitor_country}',
			'{$visitor_type_code}',
			'{$aVisitor['first_visit_at']}',
			'{$aVisitor['last_visit_at']}'
			)";
			
			$write->query($sql);
		}
		return;
	}
	
	public function doCleanHouse() {
		// remove visitor logs than are earlier than 3 months old.
		$timeSinceLastCleanUp = new DateTime('now');
		$timeSinceLastCleanUp = $timeSinceLastCleanUp->modify('-3 month');
		$timeSinceLastCleanUp = $timeSinceLastCleanUp->format('Y-m-d H:i:s');
		
		$deniedAttempts = Mage::getModel('sitesecurity/sitesecure')->getCollection()
			->setOrder('id', 'DESC')
			->addFieldToFilter('created_at ', array('lt'=> $timeSinceLastCleanUp));
		
		if (count($deniedAttempts) > 0) {
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$sitesecure_visitor_table = Mage::getSingleton('core/resource')->getTableName('sitesecurity/visitor');			
			$write->delete($sitesecure_visitor_table, new Zend_Db_Expr("created_at < '{$timeSinceLastCleanUp}'"));
		}
		return;
	}
}