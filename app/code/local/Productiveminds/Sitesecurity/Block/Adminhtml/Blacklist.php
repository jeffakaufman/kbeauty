<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Blacklist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_blacklist';
        $this->_blockGroup = 'sitesecurity';
        $this->_headerText = Mage::helper('sitesecurity')->__('Sitesecurity Blacklisted Ip Addresses');
        parent::__construct();
    }
    
    protected function _prepareLayout() {
    	$this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(array(
    					'label' => Mage::helper('sitesecurity')->__('Add a New Blacklist'),
    					'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
    					'class' => 'add'
    			))
    	);
    	
    	/**
    	 * Display store security if system has more than one store
    	 */
    	if (!Mage::app()->isSingleStoreMode()) {
    		$this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
    				->setUseConfirm(false)
    				->setSecureUrl($this->getUrl('*/*/*', array('store' => null)))
    		);
    	}
    	$this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_blacklist_grid', 'sitesecurity.blacklist.grid')->setSaveParametersInSession(true));
    	return parent::_prepareLayout();
    }
    
    public function getAddNewButtonHtml() {
    	return $this->getChildHtml('add_new_button');
    }
    
    public function getGridHtml() {
    	return $this->getChildHtml('grid');
    }
    
    public function getStoreSwitcherHtml() {
    	return $this->getChildHtml('store_switcher');
    }

}
