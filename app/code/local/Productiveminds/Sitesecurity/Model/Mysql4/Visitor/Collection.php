<?php

class Productiveminds_Sitesecurity_Model_Mysql4_Visitor_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function getVisitorInfo() {
		$this->getSelect()
		->joinLeft(
				array('luit' => 'log_url_info'),
					'luit.url_id = main_table.last_url_id',
					array('url', 'referer'))
					->order('main_table.last_visit_at DESC');
		return $this;
	}
	
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
