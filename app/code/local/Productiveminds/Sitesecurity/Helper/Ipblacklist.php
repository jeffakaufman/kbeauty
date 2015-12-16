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

class Productiveminds_Sitesecurity_Helper_Ipblacklist extends Mage_Core_Helper_Abstract
{
	
	public function isAllowed($ipAddy, $currentUrl) {
		 
		$storeId = Mage::app()->getStore()->getId();
		$accessDeniedUrl = self::getAccessDeniedUrl();
		
		$isAllowed = array(
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION => true,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL => $currentUrl,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITED_IP => $ipAddy,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITOR_URL => $currentUrl,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_TYPE_CODE => Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP
		);
		
		$ipAddy = Mage::getModel('sitesecurity/security')->getIp2long($ipAddy);
		
		$matchedBlacklists = Mage::getModel('sitesecurity/blacklist')
		->getCollection()
		->addFieldToFilter('remote_addr', $ipAddy);
		
		foreach($matchedBlacklists as $blacklist) {
			$status = $blacklist->getStatus();
			$blacklistStoreId = $blacklist->getStoreId();
			if(
					$status == Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED &&
					($blacklistStoreId == 0 || $blacklistStoreId == $storeId) &&
					!self::_getSiteSecurityHelper()->isSameDomain($accessDeniedUrl, $currentUrl)
			)
			{
				$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION] = false;
				$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL] = $accessDeniedUrl;
				
				break;
			}
		}
		return $isAllowed;
	}
	
	public function getAccessDeniedUrl() {
		$destinationType = Mage::getStoreConfig('sitesecurity_sectns/acl_ipaddress/blacklisted_ip_action_type', Mage::app()->getStore());
		$destinationUrl = self::getDestinationUrl($destinationType);
	
		return $destinationUrl;
	}
	
	protected function getDestinationUrl($destinationType) {
		$destinationUrl = Mage::getBaseUrl();
		if($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_BLANK_PAGE) {
			$destinationUrl = Mage::getBaseUrl().Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_IDENTIFIER;
		} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CMS_PAGE) {
			$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_ipaddress/blacklisted_ip_action_'.$destinationType, Mage::app()->getStore());
			$destinationUrl = Mage::getBaseUrl().$destinationUrl;
		} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CUSTOM_URL) {
			$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_ipaddress/blacklisted_ip_action_'.$destinationType, Mage::app()->getStore());
		}
		return $destinationUrl;
	}
	
	protected function _getSiteSecurityHelper() {
		return Mage::helper('sitesecurity/sitesecurity');
	}
    
}
?>