<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productiveminds/sitesecurity/visitor.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_customer_visitor_grid', 'visitor.grid'));
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
    	$this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(array(
    					'label' => Mage::helper('sitesecurity')->__('Add New'),
    					'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
    					'class' => 'add'
    			))
    	);
    	
    	if (!Mage::app()->isSingleStoreMode()) {
    		$this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
    				->setUseConfirm(false)
    				->setSecureUrl($this->getUrl('*/*/*', array('store' => null)))
    		);
    	}
    	
        $this->setChild('filterForm', $this->getLayout()->createBlock('sitesecurity/adminhtml_customer_visitor_filter'));
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChild('filterForm')->toHtml();
    }
    
    public function getAddNewButtonHtml() {
    	return $this->getChildHtml('add_new_button');
    }
    
    public function getStoreSwitcherHtml() {
    	return $this->getChildHtml('store_switcher');
    }
    
    public function getGridHtml() {
    	return $this->getChildHtml('grid');
    }

}
