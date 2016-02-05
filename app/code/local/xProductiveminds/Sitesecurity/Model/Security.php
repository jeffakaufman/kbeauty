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

class Productiveminds_Sitesecurity_Model_Security extends Mage_Core_Model_Abstract
{
	
	const BLACKLIST_ACTION_MESSAGE = 'Blacklist IP Address(s)';
	
	const BLACKLISTED_FROM_VISITOR = 'From visitor list';
	const BLACKLISTED_FROM_ORDER = 'From a completer order';
	const BLACKLISTED_FROM_REVIEW = 'From a completed product review';
	const BLACKLISTED_FROM_COUNTRY = 'Country';
	const BLACKLISTED_BY_AN_ADMIN = 'Manually by an admin';
	
	const ACL_CODE_NONE = 'admin';
	const ACL_CODE_BLACKLIST_COUNTRY = 'blacklistCountry';
	const ACL_CODE_BLACKLIST_IP = 'blacklistIp';
	const ACL_CODE_XSS = 'xssBlock';
	const ACL_CODE_SQL_INJECTION = 'sqlInjectionBlock'; // Do not delete, used in sql v1
	const ACL_CODE_EMAIL_INJECTION = 'emailInjectionBlock'; // Do not delete, used in sql v1
	const ACL_CODE_FORM_INJECTION = 'formInjectionBlock';
	
	const EMAIL_VAR_BLACKLIST_COUNTRY = 'blacklist_country';
	const EMAIL_VAR_BLACKLIST_IP = 'blacklist_ip';
	const EMAIL_VAR_XSS = 'xss_block';
	const EMAIL_VAR_FORM_INJECTION = 'form_injection_block';
	
	const ACTION_CODE_NONE = 'admin';
	const ACTION_CODE_BLANK_PAGE = 'blankpage';
	const ACTION_CODE_CMS_PAGE = 'cmspage';
	const ACTION_CODE_CUSTOM_URL = 'url';
	
	const BLANK_PAGE_IDENTIFIER = 'planned_maintenance_blk';
	const BLANK_PAGE_TITLE = 'Planned Maintenance';
	const BLANK_PAGE_TEMPLATE = 'empty';
	const BLANK_PAGE_CONTENT = '<!-- -->';
	
	const ACCESS_DENIED_URL = 'url';
	const ACCESS_DENIED_DECISION = 'acess';
	const ACCESS_DENIED_TYPE_CODE = 'denied_type_code';
	const ACCESS_DENIED_VISITED_IP = 'visitor_ip';
	const ACCESS_DENIED_VISITOR_URL = 'visitor_url';
	
	const CONTINENT_CODE_EUROPE 	= 'europe';
	const CONTINENT_CODE_N_AMERICA 	= 'nAmerica';
	const CONTINENT_CODE_AFRICA 	= 'africa';
	const CONTINENT_CODE_S_AMERICA 	= 'sAmerica';
	const CONTINENT_CODE_ANTARCTICA	= 'antarctica';
	const CONTINENT_CODE_ASIA 		= 'asia';
	const CONTINENT_CODE_OCEANIA 	= 'oceania';
	const CONTINENT_CODE_OTHER 		= 'other';


	public function getStoreFromStoreId($storeId) {
		return self::_getStoreFromStoreId($storeId);
	}
	
	protected function _getStoreFromStoreId($storeId) {
		$stores = array_keys(Mage::app()->getStores());
		foreach($stores as $thisStoreId){
			$store = Mage::app()->getStore($thisStoreId);
			if($store->getStoreId()==$storeId) {
				return $store;
			}
		}
		return false;
	}
	
	public function getWebsiteByCode($websiteCode) {
		$website = null;
		$websites = Mage::app()->getWebsites();
		foreach($websites as $aWebsite){
			if($aWebsite->getCode()==$websiteCode) {
				$website = $aWebsite;
			}
		}
		return $website;
	}
	
	public function isExcemptedIpAddress($ipAddressAddress) {
		$excemptedIpAddresses = str_replace(' ', '', Mage::getStoreConfig('sitesecurity_sectns/sitesecurity_grps/exempted_ips', Mage::app()->getStore()));
		$excemptedIpAddresses = str_replace("\n", ',', $excemptedIpAddresses);
		$excemptedIpAddresses = str_replace('&#10;', ',', $excemptedIpAddresses);
		$excemptedIpAddresses = str_replace('&#13;', ',', $excemptedIpAddresses);
		$excemptedIpAddresses = str_replace('<br>', ',', $excemptedIpAddresses);
		$excemptedIpAddresses = str_replace('<br/>', ',', $excemptedIpAddresses);
		
		$excemptedIpAddresses = explode(',', $excemptedIpAddresses);
		
		if(in_array($ipAddressAddress, $excemptedIpAddresses)) {
			return true;
		}
		
		foreach ($excemptedIpAddresses as $anExcemptedIpAddresses) {
			if( strpos($anExcemptedIpAddresses, '*') !== false ) {
				$adjustedIpAddresses = trim(str_replace('*', '255', $anExcemptedIpAddresses));
				if (filter_var($adjustedIpAddresses, FILTER_VALIDATE_IP)) {
					$lowerIp = str_replace('255', '1', $adjustedIpAddresses);
					$upperIp = $adjustedIpAddresses;
					if( self::isIpInRange($ipAddressAddress, $lowerIp, $upperIp) ) {
						return true;
					}
				}
			} elseif( strpos($anExcemptedIpAddresses, '-') !== false ) {
				$fourBlocks = explode('.', $anExcemptedIpAddresses);
				$theRange = explode('-', $fourBlocks[3]);
				$lowerIp = (int)$fourBlocks[0].'.'.$fourBlocks[1].'.'.$fourBlocks[2].'.'.$theRange[0];
				$upperIp = (int)$fourBlocks[0].'.'.$fourBlocks[1].'.'.$fourBlocks[2].'.'.$theRange[1];
				if( filter_var($lowerIp, FILTER_VALIDATE_IP) && filter_var($upperIp, FILTER_VALIDATE_IP) ) {
					if( self::isIpInRange($ipAddressAddress, $lowerIp, $upperIp) ) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	function isIpInRange($ourIp, $lowerIp, $upperIp) {
		# numeric value of ip Addresses with IP2long
		$ourValue = ip2long($ourIp);
		$lowerValue    = ip2long($lowerIp);
		$upperValue    = ip2long($upperIp);
		return (($ourValue >= $lowerValue) AND ($ourValue <= $upperValue));
	}
	
	// return long from a readable IP address
	public function getIp2long($ip='') {
		// first check IP address is valid
		if(filter_var($ip, FILTER_VALIDATE_IP)) {
			$ip = trim($ip);
			return ip2long($ip);
		}
		return 'invalid IP';
	}
	
	// return a human readable IP address from long
	public function getLong2ip($ip=0) {
		return long2ip(trim($ip));
	}

}