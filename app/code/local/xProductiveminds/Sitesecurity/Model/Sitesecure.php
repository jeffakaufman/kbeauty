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

class Productiveminds_Sitesecurity_Model_Sitesecure extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('sitesecurity/sitesecure');
	}
	
	public function denyAction($denyParams = null) {
		
		if($denyParams != null) {
			$currentStoreId = Mage::app()->getStore()->getId();
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				$customer = $customer->getId();
				$customerType = 'c';
			} else {
				$customer = 0;
				$customerType = 'v';
			}
			
			$country = Mage::helper('sitesecurity/sitesecurity')->getVisitorCountry($denyParams[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITED_IP]);
			
			$this->setTypeCode($denyParams[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_TYPE_CODE]);
			$this->setStoreId($currentStoreId);
			$this->setCustomerId($customer);
			$this->setUrl($denyParams[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITOR_URL]);
			$this->setRemoteAddr($denyParams[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITED_IP]);
			$this->setVisitorCountry($country);
			$this->setVisitorTypeCode($customerType);
			$this->save();
			
			Mage::app()->setCurrentStore($currentStoreId);
		}
	}
	
}