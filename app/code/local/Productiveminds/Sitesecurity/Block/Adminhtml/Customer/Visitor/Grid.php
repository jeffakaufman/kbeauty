<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('visitorGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('last_activity');
        $this->setDefaultDir('DESC');
    }
    
    protected function _getStore() {
    	$storeId = (int) $this->getRequest()->getParam('store', 0);
    	return Mage::app()->getStore($storeId);
    }
	
    protected function _prepareCollection()
    {
    	$collection = Mage::getModel('sitesecurity/visitor')->getCollection()->getVisitorInfo();
    	$store = $this->_getStore();
    	
    	if ($store->getId()) {
    		// if a specific store is not selected, defaults to 'all stores' - i.e all entries
    		$collection->addStoreFilter($store);
    	}
    	
        $this->setCollection($collection);
        parent::_prepareCollection();
		
        return $this;
    }
	
    protected function _prepareColumns()
    {
    	$this->addColumn('visitor_id', array(
    			'header'    => Mage::helper('sitesecurity')->__('Id'),
    			'width'     => '40px',
    			'align'     => 'right',
    			'type'      => 'number',
    			'default'   => Mage::helper('sitesecurity')->__('n/a'),
    			'index'     => 'visitor_id'
    	));
    	
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('sitesecurity')->__('Customer Id'),
            'width'     => '20px',
            'align'     => 'left',
            'type'      => 'text',
        	'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_customer',
            'index'     => 'customer_id'
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
        
        $this->addColumn('session_start_time', array(
            'header'    => Mage::helper('sitesecurity')->__('Session Start Time'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'datetime',
            'default'   => Mage::helper('sitesecurity')->__('n/a'),
            'index'     =>'first_visit_at'
        ));

        $this->addColumn('last_activity', array(
            'header'    => Mage::helper('sitesecurity')->__('Last Activity'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'datetime',
            'default'   => Mage::helper('sitesecurity')->__('n/a'),
            'index'     => 'last_visit_at'
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
        $stores = Mage::getModel('sitesecurity/system_config_source_websitestore')->toOptionArray();
        $this->addColumn('store', array(
        		'header'    => Mage::helper('sitesecurity')->__('Store Visited'),
        		'type'      => 'options',
        		'options'   => $stores,
        		'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_store',
        		'index'     => 'store_id'
        ));
        */
        
        $this->addColumn('store_id', array(
        		'header'    => Mage::helper('sales')->__('Destination (Store)'),
        		'index'     => 'store_id',
        		'type'      => 'store',
        		'store_view'=> true,
        		'display_deleted' => false,
        ));
        
        $this->addColumn('last_url', array(
            'header'    => Mage::helper('sitesecurity')->__('Last URL'),
            'type'      => 'wrapline',
            'lineLength' => '50',
            'default'   => Mage::helper('sitesecurity')->__('n/a'),
            'index'     => 'url',
            'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_url'
        ));
        
        $this->addColumn('last_url_referer', array(
        		'header'    => Mage::helper('sitesecurity')->__('Last URL Referer'),
        		'type'      => 'wrapline',
        		'lineLength' => '50',
        		'default'   => Mage::helper('sitesecurity')->__('n/a'),
        		'index'     => 'referer',
        		'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_url'
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
    			'url' => $this->getUrl('sitesecurity_admin/adminhtml_blacklist/massblacklistip'),
    			'confirm' => Mage::helper('sitesecurity')->__('Blocking multiple visitors - are you sure?')
    	));
    	
    	return $this;
    }

    public function getRowUrl($row)
    {
        return (Mage::getSingleton('admin/session')->isAllowed('customer/manage') && $row->getCustomerId())
            ? $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())) : '';
    }
}
