<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Country extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_country';
        $this->_blockGroup = 'sitesecurity';
        parent::__construct();
        $this->_headerText = Mage::helper('sitesecurity')->__('Sitesecurity');
    }
    
    public function _beforeToHtml()
    {
    	$this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_country_grid', 'sitesecurity_country_grid'));//->setSaveParametersInSession(true));
    	return parent::_beforeToHtml();
    }
    
    protected function _prepareLayout() {
    	
    	// below is removed for now, since only supporting Magento internal countries
    	//$this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
    	//		->setData(array(
    	//				'label' => Mage::helper('sitesecurity')->__('Add a New Country'),
    	//				'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
    	//				'class' => 'add'
    	//		))
    	//);
    	
    	$this->setChild('add_new_group_button', $this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(array(
    					'label' => Mage::helper('sitesecurity')->__('Add a New Continent / Group'),
    					'onclick' => "setLocation('" . $this->getUrl('sitesecurity_admin/adminhtml_countrycat/new') . "')",
    					'class' => 'add'
    			))
    	);
    	
    	/**
    	 * Display store security if system has more than one store
    	 */
    	if (!Mage::app()->isSingleStoreMode()) {
    		$this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
    			->setUseConfirm(true)
    			->setSecureUrl($this->getUrl('*/*/*', array('store' => null)))
    		);
    	}
    	//$this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_country_grid', 'sitesecurity_country_grid'));//->setSaveParametersInSession(true));
    	
    	return parent::_prepareLayout();
    }
    
    // below is removed for now, since only supporting Magento internal countries
    //public function getAddNewButtonHtml() {
    //	return $this->getChildHtml('add_new_button');
    //}
    
    public function getAddNewGroupButtonHtml() {
    	return $this->getChildHtml('add_new_group_button');
    }
    
    public function getStoreSwitcherHtml() {
    	return $this->getChildHtml('store_switcher');
    }
    
    public function getGridHtml() {
    	return $this->getChildHtml('grid');
    }

}
