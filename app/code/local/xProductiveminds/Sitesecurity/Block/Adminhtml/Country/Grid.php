<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Country_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
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
        $model = Mage::getModel('sitesecurity/country');
    	$collection = $model->getCollection();
        $store = $this->_getStore();
		
        if ($store->getId()) {
        	// ensure each country is in the selected store
        	$model->getResource()->prepareStores($store->getId());
        } 
        
        // if a specific store is not selected, defaults to 'all stores' - i.e id = 0
        $collection->addFieldToFilter('store_id', $store->getId());
        //$collection->addStoreFilter($store);
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        
    	$this->addColumn('id', array(
            'header' => Mage::helper('sitesecurity')->__('Id'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
    		'default' => ' -- '
        ));

    	
    	$countries = Mage::getModel('sitesecurity/system_config_filter_country')->toOptionArray(false);
	   	$this->addColumn('country', array(
    			'header'    => Mage::helper('sitesecurity')->__('Country'),
    			'align'     => 'left',
    			'width'     => '160',
    			'type'      => 'options',
    			'options'   => $countries,
    			'index'     => 'country',
    			'renderer'  => 'sitesecurity/adminhtml_renderer_country',
    	));
    	
    	
    	$cats = Mage::getModel('sitesecurity/system_config_filter_countrycat')->toOptionArray(false);
    	$this->addColumn('cat', array(
    			'header'    => Mage::helper('sitesecurity')->__('Continent or Group'),
    			'align'     => 'left',
    			'width'     => '100',
    			'type'      => 'options',
    			'options'   => $cats,
    			'index'     => 'cat_id',
    			'renderer'  => 'sitesecurity/adminhtml_renderer_countrycat'
    	));
    	 
    	$statuses = Mage::getSingleton('sitesecurity/system_config_source_status')->getOptionArray();
        $this->addColumn('status', array(
        		'header'    => Mage::helper('sitesecurity')->__('Status'),
        		'align'     => 'left',
        		'index'     => 'status',
        		'type'      => 'options',
        		'options'   => $statuses,
        ));
        
        
        $this->addColumn('description', array(
        		'header' => Mage::helper('sitesecurity')->__('Description'),
        		'align' => 'left',
        		'index' => 'description',
        ));
        
        
        $this->addColumn('created_at', array(
        		'header' => Mage::helper('sitesecurity')->__('Updated Date & Time'),
        		'index' => 'created_at',
        		'width' => '140px',
        		'type' => 'datetime',
        		'gmtoffset' => true
        ));
        
        /*
         DO NOT ADD Edit button since country assignment is per store view.
         It's easier to do this in the grid but not easily set individually.
         */
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
    	$this->setMassactionIdField('sitesecurity_id');
    	$this->getMassactionBlock()->setFormFieldName('sitesecuritys');
    	
    	// There is no need to delete a country. Simply allow / disallow each country.
    	//$this->getMassactionBlock()->addItem('delete', array(
    	//		'label' => Mage::helper('sitesecurity')->__('Delete'),
    	//		'url' => $this->getUrl('*/*/massDelete'),
    	//		'confirm' => Mage::helper('sitesecurity')->__('Deleting multiple items - are you sure?')
    	//));
    	
    	$statuses = Mage::getSingleton('sitesecurity/system_config_source_status')->getOptionArray();
    	
    	array_unshift($statuses, array('label'=>'', 'value'=>''));
    	
    	$this->getMassactionBlock()->addItem('status', array(
    		'label'=> Mage::helper('sitesecurity')->__('Change Status'),
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
    	
    	// Countries are only editable in All store view
    	$store = $this->_getStore();
    	if (!$store->getId()) {
    		
    		$categories = Mage::getSingleton('sitesecurity/system_config_filter_countrycat')->toOptionArray(true);
    		
    		//array_unshift($categories, array('label'=>'', 'value'=>''));
    		
    		$this->getMassactionBlock()->addItem('group', array(
    				'label'=> Mage::helper('sitesecurity')->__('Assign a Continent/Group'),
    				'url'  => $this->getUrl('*/*/massGroups', array('_current'=>true)),
    				'additional' => array(
    						'visibility' => array(
    								'name' => 'cat_id',
    								'type' => 'select',
    								'class' => 'required-entry',
    								'label' => Mage::helper('sitesecurity')->__('Group'),
    								'values' => $categories
    						)
    				)
    		));
    	}
    	
    	return $this;
    }

    public function getRowUrl($row) {
    	// Countries are only editable in All store view
    	$store = $this->_getStore();
    	if (!$store->getId()) {
    		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    	}
    	return null;
    }

}
