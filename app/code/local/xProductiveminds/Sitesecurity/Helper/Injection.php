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

class Productiveminds_Sitesecurity_Helper_Injection extends Mage_Core_Helper_Abstract
{   
	public function isAllowed($ipAddy, $currentUrl) {
    	
		$postParams = Mage::app()->getFrontController()->getRequest()->getPost();
		$getParams = Mage::app()->getFrontController()->getRequest()->getParams();
		
		
		if( empty($postParams) && empty($getParams) ) {
			$isAllowed = array(Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION => true);
		} else {
			$accessDeniedUrl = self::getAccessDeniedUrl();
			$isAllowed = array(
				Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION => true,
				Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL => $currentUrl,
				Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITED_IP => $ipAddy,
				Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_VISITOR_URL => $currentUrl,
				Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_TYPE_CODE => Productiveminds_Sitesecurity_Model_Security::ACL_CODE_FORM_INJECTION
			);
			
			$pmsKnownPatterns = array(
					'/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/i', // SQL Injection check
					'/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix', // SQL Injection check
					'/((\%3C)|<)[^\n]+((\%3E)|>)/i', // paranoid xss check
					'/((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/i', // '<img src' xss check
				);
			$userKnownPatterns = self::isAnInjectioQuery();
			$injections = array_merge($userKnownPatterns, $pmsKnownPatterns);
			
			$requestParams = array();
			if( count($postParams) > 0 ) {
				$requestParams = array_merge($postParams, $requestParams);
			}
			if( count($getParams) > 0 ) {
				$requestParams = array_merge($getParams, $requestParams);
			}
			
			if(count($requestParams) > 0) {
				foreach ($requestParams as $requestParam) {
					//check if there is a pattern match in '$injections' with this postParam
					foreach ($injections as $injection) {
						if ($injection != ''){
							if (preg_match($injection, $requestParam)) {
								$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_DECISION] = false;
								$isAllowed[Productiveminds_Sitesecurity_Model_Security::ACCESS_DENIED_URL] = $accessDeniedUrl;
								break;
							}
						}
					}
				}
			}
		}
    	return $isAllowed;
    }
    
    public function isAnInjectioQuery() {
    	$isAnInjectioQuery = str_replace(' ', '', Mage::getStoreConfig('sitesecurity_sectns/acl_injectio/injectio_queries', Mage::app()->getStore()));
    	$isAnInjectioQuery = str_replace("\n", '||||', $isAnInjectioQuery);
    	$isAnInjectioQuery = str_replace('&#10;', '||||', $isAnInjectioQuery);
    	$isAnInjectioQuery = str_replace('&#13;', '||||', $isAnInjectioQuery);
    	$isAnInjectioQuery = str_replace('<br>', '||||', $isAnInjectioQuery);
    	$isAnInjectioQuery = str_replace('<br/>', '||||', $isAnInjectioQuery);
    	
    	$isAnInjectioQuery = explode('||||', $isAnInjectioQuery);
    	return $isAnInjectioQuery;
    }
    
    public function getAccessDeniedUrl() {
    	$destinationType = Mage::getStoreConfig('sitesecurity_sectns/acl_injectio/injectio_action_type', Mage::app()->getStore());
    	$destinationUrl = self::getDestinationUrl($destinationType);
    	 
    	return $destinationUrl;
    }
    
    protected function getDestinationUrl($destinationType) {
    	$destinationUrl = Mage::getBaseUrl();
    	if($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_BLANK_PAGE) {
    		$destinationUrl = Mage::getBaseUrl().Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_IDENTIFIER;
    	} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CMS_PAGE) {
    		$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_injectio/injectio_action_'.$destinationType, Mage::app()->getStore());
    		$destinationUrl = Mage::getBaseUrl().$destinationUrl;
    	} elseif($destinationType == Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_CUSTOM_URL) {
    		$destinationUrl = Mage::getStoreConfig('sitesecurity_sectns/acl_injectio/injectio_action_'.$destinationType, Mage::app()->getStore());
    	}
    	return $destinationUrl;
    }
    
    protected function _getSiteSecurityHelper() {
    	return Mage::helper('sitesecurity/sitesecurity');
    }
    
}
?>