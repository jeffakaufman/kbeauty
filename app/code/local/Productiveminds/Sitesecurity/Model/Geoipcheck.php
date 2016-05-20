<?php

if(!function_exists('geoip_load_shared_mem')) {
	include_once("Productiveminds/MaxMind/geoip/geoip.php");
	include_once("Productiveminds/MaxMind/geoip/geoipcity.php");
	include_once("Productiveminds/MaxMind/geoip/geoipregionvars.php");
}

class Productiveminds_Sitesecurity_Model_Geoipcheck
{
	protected $_siteSecuritySession;
	
    public function __construct()
    {
        $this->_siteSecuritySession = Mage::getSingleton('core/session');
    }

    /**
     * Detect country with geo-ip API
     *
     * @return array $result
     */
    public function getGeoIp($remoteAddr = '0') {
        $result = array(
        	'country_id'            => null,
        	'city'                  => null,
          	'region_id'             => null,
          	'postcode'              => null
        );
      	$detectCountry  = Mage::getStoreConfig('sitesecurity_sectns/geo_ip/country');
		$detectRegion   = Mage::getStoreConfig('sitesecurity_sectns/geo_ip/region');
       	$detectCity     = Mage::getStoreConfig('sitesecurity_sectns/geo_ip/city');
      	$geoipEnabled  = true;
      	
            if ($detectCountry || $detectRegion || $detectCity) {
                if (!function_exists('geoip_open')) {
                    $geoipEnabled = false;
                    $this->_siteSecuritySession->addError(
                        Mage::helper('sitesecurity')->__("GeoIP is enabled but geoip_open function is not found")
                    );
                }
            }
			
            if($remoteAddr == '0') {
            	$remoteAddr = Mage::helper('core/http')->getRemoteAddr();
            }
            
            $filename = '';
            $fileDir = Mage::getBaseDir('lib')
                    . DS
            		. "Productiveminds/MaxMind/data/";
            
            if ($detectCountry && $geoipEnabled) {                
                if( is_dir($fileDir) ) {
                	$filename = $fileDir . Mage::getStoreConfig('sitesecurity_sectns/geo_ip/country_file');
	                if (is_readable($filename)) {
	                    $gi = geoip_open($filename, GEOIP_STANDARD);
	                    $result['country_id'] = geoip_country_code_by_addr($gi, $remoteAddr);
	                    geoip_close($gi);
	                } else {
	                    $errorMsg = Mage::helper('sitesecurity')->__(
	                            "Country detection is enabled but %s not found", Mage::getStoreConfig('sitesecurity_sectns/geo_ip/country_file')
	                        );
	                }	                
	            } else {
	            	$errorMsg = Mage::helper('sitesecurity')->__( "Country detection is enabled but folder '%s' is does not exist", $fileDir );
	            }
            }
            
            if ($detectRegion && $geoipEnabled) {
                if( is_dir($fileDir) ) {
                	$filename = $fileDir . Mage::getStoreConfig('sitesecurity_sectns/geo_ip/region_file');
                	//chmod($filename, 0777);
	                if (is_readable($filename)) {
	                    $gi = geoip_open($filename, GEOIP_STANDARD);
	                    list($countryCode, $regionCode) = geoip_region_by_addr($gi, $remoteAddr);
	                    $region = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
	                    $result['country_id'] = $countryCode;
	                    $result['region_id'] = $region->getId();
	                    geoip_close($gi);
	                } else {
	                    $this->_siteSecuritySession->addError(
	                        Mage::helper('sitesecurity')->__(
	                            "Region detection is enabled but %s not found", Mage::getStoreConfig('sitesecurity_sectns/geo_ip/region_file')
	                        )
	                    );
	                }
                } else {
                	$errorMsg = Mage::helper('sitesecurity')->__( "Country detection is enabled but folder '%s' is does not exist", $fileDir );
                }
            }
            
            if ($detectCity && $geoipEnabled) {
                if( is_dir($fileDir) ) {
                	$filename = $fileDir . Mage::getStoreConfig('sitesecurity_sectns/geo_ip/city_file');
                	//chmod($filename, 0777);
	                if (is_readable($filename)) {
	                    $gi = geoip_open($filename, GEOIP_STANDARD);
	                    $record = geoip_record_by_addr($gi, $remoteAddr);
	                    if ($record) {
	                        $result['city'] = $record->city;
	                        $result['postcode'] = $record->postal_code;
	                    }
	                    geoip_close($gi);
	                } else {
	                    $this->_siteSecuritySession->addError(
	                        Mage::helper('sitesecurity')->__(
	                            "City detection is enabled but %s not found", Mage::getStoreConfig('sitesecurity_sectns/geo_ip/city_file')
	                        )
	                    );
	                }
                } else {
                	$errorMsg = Mage::helper('sitesecurity')->__( "Country detection is enabled but folder '%s' is does not exist", $fileDir );
                }
            }
        	return $result;
   		}
    	
}
