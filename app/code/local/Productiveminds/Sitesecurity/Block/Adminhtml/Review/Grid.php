<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Review_Grid
{

	protected function _prepareMassaction() {
		
		parent::_prepareMassaction();
		
		if ( Mage::helper('sitesecurity')->isModuleActive() ) {
			$this->getMassactionBlock()->addItem('blacklist_id', array(
					'label' => Mage::helper('sitesecurity')->__(Productiveminds_Sitesecurity_Model_Security::BLACKLIST_ACTION_MESSAGE),
					'url' => $this->getUrl('sitesecurity_admin/adminhtml_blacklist/blacklistipfromreview'),
					'confirm' => Mage::helper('sitesecurity')->__('Blocking multiple visitors - are you sure?')
			));
		}
	}
	
	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		
		$this->addColumn('ip', array(
				'header'    => Mage::helper('sitesecurity')->__('IP Address'),
				'default'   => Mage::helper('sitesecurity')->__('n/a'),
				'index'     => 'pms_sitesecurity_ip',
				'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_ip',
				'filter'    => false,
				'sort'      => false
		));
		
	}
    
}
