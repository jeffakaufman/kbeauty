<?php

class Productiveminds_Sitesecurity_Model_Mysql4_Country extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('sitesecurity/country', 'id');
	}
	
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$sql = "SELECT * FROM `pms_sitesecurity_country` WHERE country = '{$object->getData('country')}'";
		$countryStores = $read->fetchAll($sql);
		
		// A country must belong to the same category in all storeviews
		foreach ($countryStores as $countryStore) {
			$sql = "UPDATE `pms_sitesecurity_country` SET
			`cat_id` = '{$object->getData('cat_id')}' 
			WHERE  `id` = '{$countryStore['id']}'";
			$write->query($sql);
		}
		return parent::_afterSave($object);
	}
	
	public function prepareStores($storeId) {
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$countries = Mage::getModel('sitesecurity/country')->getCollection();
		$countries->addFieldToFilter('store_id', 0);
		foreach ($countries as $country) {
			
			$sql = "SELECT * FROM `pms_sitesecurity_country` WHERE country = '{$country->getCountry()}' AND store_id = '{$storeId}'";
			$countryStores = $read->fetchAll($sql);
			
			if(null==$countryStores || empty($countryStores) || count($countryStores) < 1) {
				$arrayObj = array();
				$arrayObj['country'] = $country->getCountry();
				$arrayObj['cat_id'] = $country->getCatId();
				$arrayObj['store_id'] = $storeId;
				$arrayObj['status'] = 1;
				$this->_getWriteAdapter()->insert($this->getTable('country'), $arrayObj);
			}
		}
	}
	
}