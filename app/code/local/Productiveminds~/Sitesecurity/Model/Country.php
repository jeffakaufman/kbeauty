<?php

class Productiveminds_Sitesecurity_Model_Country extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('sitesecurity/country');
		
		self::prepare();
	}
	
	protected function prepare() {
		
		
		$collection = $this->getCollection()
		->setOrder('id', 'DESC')
		->setPageSize(1)
		->setCurPage(1);
		
		if(!empty($collection) && null != $collection && count($collection) > 0) {
			// table is already populated
			return;
		}
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		// record all countries
		$allCountries = Mage::getModel('directory/country')->getCollection();
		$securityHelper = Mage::helper('sitesecurity/countrycat');
		foreach ($allCountries as $aCountry) {
			$contryCode = $aCountry->getCountryId();
			$continent = $securityHelper->getCountryContinent($contryCode);
		
			$sql = "INSERT INTO `pms_sitesecurity_country`
			(`country`, `store_id`, `cat_id`, `description`, `status`) 
			values
			('{$contryCode}', 0, '{$continent}', 'Added during installation', 1)";
					
			$write->query($sql);
		}
	}
}