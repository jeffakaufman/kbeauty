<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Sitesecure_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sitesecurityGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _getStore() {
    	$storeId = (int) $this->getRequest()->getParam('store', 0);
    	return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('sitesecurity/sitesecure')->getCollection();
        $store = $this->_getStore();
		
        if ($store->getId()) {
        	// if a specific store is not selected, defaults to 'all stores' - i.e all entries
        	$collection->addStoreFilter($store);
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        
    	$this->addColumn('id', array(
            'header' => Mage::helper('sitesecurity')->__('Id'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id'
        ));
    	
    	$typeCodes = Mage::getModel('sitesecurity/system_config_filter_acl')->toOptionArray();
    	$this->addColumn('type_code', array(
    			'header' => Mage::helper('sitesecurity')->__('Type of Denial'),
    			'align' => 'left',
    			'index' => 'type_code',
    			'type'      => 'options',
    			'options'   => $typeCodes,
    			'renderer'  => 'sitesecurity/adminhtml_renderer_acl',
    	));
    	
    	$this->addColumn('ip_address', array(
    			'header'    => Mage::helper('sitesecurity')->__('IP Address'),
    			'default'   => Mage::helper('sitesecurity')->__('n/a'),
    			'index'     => 'remote_addr',
    			'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_ip',
    			'filter'    => false,
    			'sort'      => false
    	));
    	
    	$countries = Mage::getModel('sitesecurity/system_config_filter_country')->toOptionArray(true);
    	$this->addColumn('country', array(
    			'header'    => Mage::helper('sitesecurity')->__("Visitor's Country"),
    			'align'     => 'left',
    			'width'     => '40',
    			'type'      => 'options',
    			'options'   => $countries,
    			'index'     => 'visitor_country'
    	));
    	
    	$stores = Mage::getModel('sitesecurity/system_config_source_websitestore')->toOptionArray();
    	$this->addColumn('store', array(
    			'header'    => Mage::helper('sitesecurity')->__('Store Visited'),
    			'type'      => 'options',
    			'options'   => $stores,
    			'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_store',
    			'index'     => 'store_id'
    	));
    	
    	$typeOptions = array(
    			Productiveminds_Sitesecurity_Model_Visitor::VISITOR_TYPE_CUSTOMER => Mage::helper('sitesecurity')->__('Customer'),
    			Productiveminds_Sitesecurity_Model_Visitor::VISITOR_TYPE_VISITOR  => Mage::helper('sitesecurity')->__('Visitor'),
    	);
    	$this->addColumn('type', array(
    			'header'    => Mage::helper('sitesecurity')->__('Visitor Type'),
    			'type'      => 'options',
    			'options'   => $typeOptions,
    			'index'     => 'visitor_type_code'
    	));
    	
    	/*
    	$this->addColumn('customer_id', array(
    			'header'    => Mage::helper('sitesecurity')->__('Customer Id'),
    			'width'     => '20px',
    			'align'     => 'left',
    			'type'      => 'text',
    			'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_customer',
    			'index'     => 'customer_id'
    	));
        */
    	
        $this->addColumn('created_at', array(
        		'header' => Mage::helper('sitesecurity')->__('First Attempt Time'),
        		'index' => 'created_at',
        		'width' => '140px',
        		'type' => 'datetime',
        		'gmtoffset' => true
        ));
        
        /*
        $this->addColumn('updated_at', array(
        		'header' => Mage::helper('sitesecurity')->__('Last Attempt Time'),
        		'index' => 'updated_at',
        		'width' => '140px',
        		'type' => 'datetime',
        		'gmtoffset' => true
        ));
        */
        
        $this->addColumn('url', array(
        		'header' => Mage::helper('sitesecurity')->__('First Attempted URL'),
        		'align' => 'left',
        		'type'      => 'wrapline',
        		'lineLength' => '50',
        		'index' => 'url'
        ));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
    	$this->setMassactionIdField('sitesecurity_id');
    	$this->getMassactionBlock()->setFormFieldName('sitesecuritys');
    	
    	$this->getMassactionBlock()->addItem('delete', array(
    			'label' => Mage::helper('sitesecurity')->__('Delete'),
    			'url' => $this->getUrl('*/*/massDelete'),
    			'confirm' => Mage::helper('sitesecurity')->__('Deleting multiple items - are you sure?')
    	));
    	
    	$this->getMassactionBlock()->addItem('blacklist_id', array(
    			'label' => Mage::helper('sitesecurity')->__(Productiveminds_Sitesecurity_Model_Security::BLACKLIST_ACTION_MESSAGE),
    			'url' => $this->getUrl('adminhtml/adminhtml_blacklist/massblacklistattempt'),
    			'confirm' => Mage::helper('sitesecurity')->__('Blocking multiple visitors - are you sure?')
    	));
    	
    	return $this;
    }

    public function getRowUrl($row) {
    	return (Mage::getSingleton('admin/session')->isAllowed('customer/manage') && $row->getCustomerId())
    	? $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())) : '';
    }

}
