<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Sitesecure extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_sitesecure';
        $this->_blockGroup = 'sitesecurity';
        $this->_headerText = Mage::helper('sitesecurity')->__('Sitesecurity Denied Requests');
        parent::__construct();
    }
    
    protected function _prepareLayout() {
    	
    	/**
    	 * Display store security if system has more than one store
    	 */
    	if (!Mage::app()->isSingleStoreMode()) {
    		$this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
    				->setUseConfirm(false)
    				->setSecureUrl($this->getUrl('*/*/*', array('store' => null)))
    		);
    	}
    	$this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_sitesecure_grid', 'sitesecurity.grid')->setSaveParametersInSession(true));
    	return parent::_prepareLayout();
    }
  	
    public function getStoreSwitcherHtml() {
    	return $this->getChildHtml('store_switcher');
    }
    
    public function getGridHtml() {
    	return $this->getChildHtml('grid');
    }

}
