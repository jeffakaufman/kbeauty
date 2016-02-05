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

class Productiveminds_Sitesecurity_Model_Visitor extends Mage_Core_Model_Abstract
{
	const VISITOR_TYPE_CUSTOMER = 'c';
	const VISITOR_TYPE_VISITOR  = 'v';
	
	protected function _construct()
	{
		$this->_init('sitesecurity/visitor', 'visitor_id');
		
		//self::prepare();
	}
	
	protected function prepare()
	{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$collection = $this->getCollection()
			->setOrder('visitor_id', 'DESC')
			->setPageSize(1)
            ->setCurPage(1);
		
		$lastRecordedVisitorId = 0;
		if(!empty($collection) && null != $collection && count($collection) > 0) {
			$lastRecordedVisitorId = $collection->getFirstItem()->getVisitorId();
		}
		
		// do it anyway, for the first time
		if (count($this->getCollection()) == 0 ) {
			$lastRecordedVisitorId = 0;
		}
		
		
		$sql = "SELECT lvt.*, lvit.server_addr, lvit.remote_addr, cust.customer_id
		FROM `log_visitor` AS `lvt`
		JOIN `log_visitor_info` AS `lvit`
		ON lvit.visitor_id = lvt.visitor_id 
		LEFT JOIN `log_customer` AS `cust`
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
			$visitor_country = Mage::helper('sitesecurity/sitesecurity')->getVisitorCountry($aVisitor['remote_addr']);
			
			$sql = "INSERT INTO `pms_sitesecurity_visitor`
			(visitor_id, store_id, customer_id, last_url_id, session_id, server_addr, remote_addr, visitor_country, visitor_type_code, first_visit_at, last_visit_at) values
			(
			'{$aVisitor['visitor_id']}',
			'{$aVisitor['store_id']}',
			'{$aVisitor['customer_id']}',
			'{$aVisitor['last_url_id']}',
			'{$aVisitor['session_id']}',
			'{$aVisitor['server_addr']}',
			'{$aVisitor['remote_addr']}',
			'{$visitor_country}',
			'{$visitor_type_code}',
			'{$aVisitor['first_visit_at']}',
			'{$aVisitor['last_visit_at']}'
			)";
			
			$write->query($sql);
		}
	}
	
	/* Add Filter by status
	*
	* @param int $visitor_id
	* @return Productiveminds_Sitesecurity_Model_Visitor
	*/
	public function addVisitorInfoFilter($visitor_id) {
		$this->getSelect()->where('main_table.visitor_id = ?', $visitor_id);
		return $this;
	}

}