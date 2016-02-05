<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Countrycat extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_countrycat';
        $this->_blockGroup = 'sitesecurity';
        $this->_headerText = Mage::helper('sitesecurity')->__('Sitesecurity');
        parent::__construct();
    }
    
    protected function _prepareLayout() {
    	
    	$this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(array(
    					'label' => Mage::helper('sitesecurity')->__('Add New'),
    					'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
    					'class' => 'add'
    			))
    	);
    	
    	$this->setChild('grid', $this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_grid', 'sitesecurity.countrycat.grid'));     
    	return parent::_prepareLayout();
    }
    
    public function getAddNewButtonHtml() {
    	return $this->getChildHtml('add_new_button');
    }
    
    public function getGridHtml() {
    	return $this->getChildHtml('grid');
    }

}
