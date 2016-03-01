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

class Productiveminds_Sitesecurity_Helper_Xss extends Mage_Core_Helper_Abstract
{
    
	public function isAllowed($ipAddy, $currentUrl) {
    	
		$accessDeniedUrl = self::getAccessDeniedUrl();

		$isAllowed = array(
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION => true,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL => $currentUrl,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITED_IP => $ipAddy,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITOR_URL => $currentUrl,
			Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_TYPE_CODE => Productiveminds_Sitesecurity_Model_Security::ACL_CODE_XSS
		);
		
		$urlParams = @parse_url($currentUrl);
		
		$pmsKnownPatterns = array(
			'/((\%3C)|<)[^\n]+((\%3E)|>)/i', // paranoid xss injection
			'/((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/i', // '<img src' xss request
		);
		
		// if there is a pattern match
		if (array_key_exists('query', $urlParams) || array_key_exists('path', $urlParams)) {
			
			$urlText = ltrim($urlParams['path'], '/');
			if(array_key_exists('query', $urlParams)) {
				$urlText .= $urlParams['query'];
			}
			
			$returnNow = false;
			foreach ($pmsKnownPatterns as $pmsKnownPattern) {
				if (preg_match($pmsKnownPattern, $urlText)) {
					$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION] = false;
					$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL] = $accessDeniedUrl;
					$returnNow = true;
					break;
				}
			}
			if($returnNow) { 
				return $isAllowed;
			}
			
			$userKnownBlackLists = self::isAnXssQuery();
			$pmsKnownBlackLists = array('</', '%3C/', 'script>', 'script%3E', 'SCRIPT>', 'SCRIPT%3E', 'alert', 'cookie', 'script >', 'script%20%3E', 'SCRIPT >', 'SCRIPT%20%3E', 'var ', 'onfocus', 'onclick', 'sql', 'document.');
			$blackLists = array_merge($userKnownBlackLists, $pmsKnownBlackLists);
			 
			foreach ($blackLists as $aBlackList) {
				if( (strpos($urlText, $aBlackList) !== false) && !self::_getSiteSecurityHelper()->isSameDomain($accessDeniedUrl, $currentUrl) ) {
					$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION] = false;
					$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL] = $accessDeniedUrl;
					break;
				}
			}
		}
		
    	return $isAllowed;
    }
    
    public function isAnXssQuery() {
    	$isAnXssQuery = str_replace(' ', '', Mage::getStoreConfig('sitesecurity_sectns/acl_xss/xss_queries', Mage::app()->getStore()));
    	$isAnXssQuery = str_replace("\n", ',', $isAnXssQuery);
    	$isAnXssQuery = str_replace('&#10;', ',', $isAnXssQuery);
    	$isAnXssQuery = str_replace('&#13;', ',', $isAnXssQuery);
    	$isAnXssQuery = str_replace('<br>', ',', $isAnXssQuery);
    	$isAnXssQuery = str_replace('<br/>', ',', $isAnXssQuery);
    	
    	$isAnXssQuery = explode(',', $isAnXssQuery);
    	return $isAnXssQuery;
    }
    
    public function getAccessDeniedUrl() {
    	$destinationType = Mage::getStoreConfig('sitesecurity_sectns/acl_xss/xss_action_type', Mage::app()->getStore());
    	$destinationUrl = self::getDestinationUrl($destinationType);
    	 
    	return $destinationUrl;
    }
    
    protected function getDestinationUrl($destinationType) {
    	$destinationUrl = Mage::getBaseUrl();
    	if($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_BLANK_PAGE) {
    		$destinationUrl = Mage::getBaseUrl().Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_IDENTIFIER;
    	} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CMS_PAGE) {
    		$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_xss/xss_action_'.$destinationType, Mage::app()->getStore());
    		$destinationUrl = Mage::getBaseUrl().$destinationUrl;
    	} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CUSTOM_URL) {
    		$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_xss/xss_action_'.$destinationType, Mage::app()->getStore());
    	}
    	return $destinationUrl;
    }
    
    protected function _getSiteSecurityHelper() {
    	return Mage::helper('sitesecurity/sitesecurity');
    }
    
}
?>