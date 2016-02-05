<?php

class Productiveminds_Sitesecurity_Model_Resource_Review_Product_Collection extends Mage_Review_Model_Resource_Review_Product_Collection
{	
	
	protected function _joinFields()
	{
		$reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
		$reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');
	
		$this->addAttributeToSelect('name')
		->addAttributeToSelect('sku');
	
		$this->getSelect()
		->join(array('rt' => $reviewTable),
				'rt.entity_pk_value = e.entity_id',
				array('rt.review_id', 'review_created_at'=> 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id'))
				->join(array('rdt' => $reviewDetailTable),
						'rdt.review_id = rt.review_id',
						array('rdt.title','rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id', 'pms_sitesecurity_ip'));
		return $this;
	}
}
