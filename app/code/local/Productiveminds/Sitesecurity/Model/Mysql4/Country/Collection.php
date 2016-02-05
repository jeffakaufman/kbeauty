<?php

class Productiveminds_Sitesecurity_Model_Mysql4_Country_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function addStoreFilter($store = null) {
		if ($store === null) {
			$store = Mage::app()->getStore(0);
		}
		if (!Mage::app()->isSingleStoreMode()) {
			if ($store instanceof Mage_Core_Model_Store) {
				$storeId = array($store->getId());
			}
			$this->getSelect()->where('main_table.store_id in (?)', $storeId);
		}
		return $this;
	}
}
