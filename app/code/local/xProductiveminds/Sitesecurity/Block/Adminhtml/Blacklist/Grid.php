<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Blacklist_Grid extends Mage_Adminhtml_Block_Widget_Grid {

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
        $collection = Mage::getModel('sitesecurity/blacklist')->getCollection();
        $store = $this->_getStore();

        if ($store->getId()) {
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
    	
    	$this->addColumn('user', array(
    			'header' => Mage::helper('sitesecurity')->__('Blacklister'),
    			'align' => 'left',
    			'index' => 'user_id',
    			'renderer'  => 'sitesecurity/adminhtml_renderer_user',
    	));
        
    	$this->addColumn('blacklisted_from', array(
    			'header' => Mage::helper('sitesecurity')->__('Blacklisted From or By'),
    			'align' => 'left',
    			'index' => 'blacklisted_from',
    	));

    	$statuses = Mage::getSingleton('sitesecurity/system_config_source_status')->toOptionEnableArray();
        $this->addColumn('status', array(
        		'header'    => Mage::helper('sitesecurity')->__('Status'),
        		'align'     => 'left',
        		'index'     => 'status',
        		'type'      => 'options',
        		'options'   => $statuses
        ));
        
        $this->addColumn('ip_address', array(
        		'header'    => Mage::helper('sitesecurity')->__('IP Address'),
        		'default'   => Mage::helper('sitesecurity')->__('n/a'),
        		'index'     => 'remote_addr',
        		'renderer'  => 'sitesecurity/adminhtml_customer_visitor_grid_renderer_ip',
        		'filter'    => false,
        		'sort'      => false
        ));
        
        $this->addColumn('created_at', array(
        		'header' => Mage::helper('sitesecurity')->__('Updated Date & Time'),
        		'index' => 'created_at',
        		'width' => '140px',
        		'type' => 'datetime',
        		'gmtoffset' => true
        ));
        
        $this->addColumn('action', array(
        		'header' => Mage::helper('sitesecurity')->__('Actions'),
        		'width' => '100',
        		'type' => 'action',
        		'getter' => 'getId',
        		'actions' => array(
        			array(
        				'caption' => Mage::helper('sitesecurity')->__('Edit'),
        				'url' => array('base' => '*/*/edit'),
        				'field' => 'id'
        			)
        		),
        		'filter' => false,
        		'sortable' => false,
        		'index' => 'stores',
        		'is_system' => true,
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
    	
    	$statuses = Mage::getSingleton('sitesecurity/system_config_source_status')->getOptionArray();
    	
    	array_unshift($statuses, array('label'=>'', 'value'=>''));
    	
    	$this->getMassactionBlock()->addItem('status', array(
    			'label'=> Mage::helper('sitesecurity')->__('Change status'),
    			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
    			'additional' => array(
    					'visibility' => array(
    							'name' => 'status',
    							'type' => 'select',
    							'class' => 'required-entry',
    							'label' => Mage::helper('sitesecurity')->__('Status'),
    							'values' => $statuses
    					)
    			)
    	));
    	return $this;
    }

    public function getRowUrl($row) {
    	return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
