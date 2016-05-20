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

class Productiveminds_Sitesecurity_Model_Observer
{
	public function saveAfterCustomerOrder(Varien_Event_Observer $observer) {
		
		if ( Mage::helper('sitesecurity')->isModuleActive() ) {
			
			$order = $observer->getEvent()->getOrder();
			$orderStoreId = $order->getStoreId();
			$ipAddy = Mage::helper('core/http')->getRemoteAddr();
			$ipAddy = Mage::getModel('sitesecurity/security')->getIp2long($ipAddy);
			
			if (!$order) {
				return $this;
			}
			try {
				$order->setPmsSitesecurityIp($ipAddy);
				$order->save();
			} catch (Exception $e) {
				Mage::log('Productiveminds_Sitesecurity_Model_Observer::saveAfterCustomerOrder - failed. Unable to add customer IP address to order', null, 'Productiveminds_Sitesecurity.log');
			}
		}
	}
	
	public function blockAllSpecified($observer = null) {
		
		if ( Mage::helper('sitesecurity')->isModuleActive() ) {
			
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			$ipAddy = Mage::helper('core/http')->getRemoteAddr();
			
			// check if it is an XSS request
			$isAllowedData = self::_getXssHelper()->isAllowed($ipAddy, $currentUrl);
			if(!$isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION]) {
				self::doDeniedRedirect($isAllowedData);
				exit;
			}
			
			// check if it is a form Injection request
			$isAllowedData = self::_getInjectionHelper()->isAllowed($ipAddy, $currentUrl);
			if(!$isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION]) {
				self::doDeniedRedirect($isAllowedData);
				exit;
			}
			
			// check if user is from a blacklisted country
			$isAllowedData = self::_getCountryBlacklistHelper()->isAllowed($ipAddy, $currentUrl);
			if(!$isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION]) {
				self::doDeniedRedirect($isAllowedData);
				exit;
			}
			
			// check if user is from a blacklisted Ip Address
			$isAllowedData = self::_getIpBlacklistHelper()->isAllowed($ipAddy, $currentUrl);
			if(!$isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION]) {
				self::doDeniedRedirect($isAllowedData);
				exit;
			}
		}
	}
	
	protected function doDeniedRedirect($isAllowedData) {
		$isPermittedRequest = $isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION];
		if (!$isPermittedRequest) {
			$accessDeniedUrl = $isAllowedData[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL];
			if (!$accessDeniedUrl) {
				$accessDeniedUrl = Mage::getUrl('');
			}
			// record denied access 
			self::_getSecureModel()->denyAction($isAllowedData);
			
			// redirect to blocked page
			Mage::app()->getFrontController()->getResponse()
			->setRedirect($accessDeniedUrl)
			->sendResponse();
			exit;
		}
	}
	
	protected function _getSecureModel() {
		return Mage::getModel('sitesecurity/sitesecure');
	}
	
	protected function _getSiteSecurityHelper() {
		return Mage::helper('sitesecurity/sitesecurity');
	}
	
	protected function _getXssHelper() {
		return Mage::helper('sitesecurity/xss');
	}
	
	protected function _getInjectionHelper() {
		return Mage::helper('sitesecurity/injection');
	}
	
	protected function _getCountryBlacklistHelper() {
		return Mage::helper('sitesecurity/countryblacklist');
	}
	
	protected function _getIpBlacklistHelper() {
		return Mage::helper('sitesecurity/ipblacklist');
	}
}