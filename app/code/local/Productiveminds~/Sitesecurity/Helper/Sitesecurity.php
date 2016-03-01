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

class Productiveminds_Sitesecurity_Helper_Sitesecurity extends Mage_Core_Helper_Abstract
{
    
    /**
     * Renders testing
     *
     * @param string $coreRoute
     */
    public function getGeoIp($ipAddress = null)
    {
    	$remoteAddr = Mage::helper('core/http')->getRemoteAddr();
    	if(null != $ipAddress) {
    		$remoteAddr = $ipAddress;
    	}
    	return Mage::getModel('sitesecurity/geoipcheck')->getGeoIp($remoteAddr);
    }
    
    /**
     * Renders user country
     *
     * @param string $coreRoute
     */
    public function getGeoIpCountryId($ipAddress = null) {
    	$localeObject = self::getGeoIp($ipAddress);
    	return strtoupper($localeObject['country_id']);
    }
    
    public function getVisitorCountry($remote_Address = null)
    {
    	$ipAddy = long2ip(trim($remote_Address));
    	$visitorCountryCode = self::getGeoIpCountryId($ipAddy);
    	if( $ipAddy == '127.0.0.1' ) {
    		return 'local'; // Visitor Browsed from Store Server
    	} else if(!empty($visitorCountryCode) && $visitorCountryCode != '') {
    		return strtoupper($visitorCountryCode);
    	} else {
    		return 'unk'; // ie unknown
    	}
    }
        
    protected function getStoreBaseUrl($storeId) {
    	if(Mage::app()->getStore()->isCurrentlySecure()) {
    		return Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, Mage::getModel('sitesecurity/observer')->getStoreIdFromStoreCode($storeId));
    	} else {
    		return Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, Mage::getModel('sitesecurity/observer')->getStoreIdFromStoreCode($storeId));
    	}
    }
    
    public function isCrawlerAgent() {
    	$bots_list = explode(',', Mage::getStoreConfig('sitesecurity_sectns/sitesecurity_grps/crawler_agents', Mage::app()->getStore()));
    	$regexp = '/'.  implode("|", $bots_list).'/';
    	$ua = $_SERVER['HTTP_USER_AGENT'];
    	if(preg_match($regexp, $ua, $matches)){
    		return TRUE;
    	}
    	else {
    		return FALSE;
    	}
    }
    
    public function isPhysicalResource() {
    	// check if a directory or file
    	$baseDir = rtrim(Mage::getBaseDir(), '/');
    	$baseUrl = rtrim(Mage::getBaseUrl(), '/');
    	$currentUrl = rtrim(Mage::helper('core/url')->getCurrentUrl(), '/');
    	$physicalVersionOfUrl = str_replace($baseUrl, $baseDir, $currentUrl);
    	if($currentUrl != $baseUrl && (is_dir($physicalVersionOfUrl) || is_file($physicalVersionOfUrl))) {
    		return false;
    	}
    	return true;
    }
    
    public function isSameDomain($domainOne, $domainTwo) {
    	$domainOne = str_replace('https', 'http', $domainOne);
    	$domainOne = rtrim($domainOne, '/');
    	$domainTwo = str_replace('https', 'http', $domainTwo);
    	$domainTwo = rtrim($domainTwo, '/');
    	if($domainOne == $domainTwo) {
    		return true;
    	}
    	return false;
    }
}
?>