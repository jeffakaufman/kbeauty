<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Countrycat_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() {
        parent::__construct();
        $this->setId('sitesecurityGrid');
        $this->setDefaultSort('cat_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection() {
        $collection = Mage::getModel('sitesecurity/countrycat')->getCollection();
     	$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        
    	$this->addColumn('cat_id', array(
            'header' => Mage::helper('sitesecurity')->__('Id'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'cat_id'
        ));
    	
    	$this->addColumn('title', array(
    			'header' => Mage::helper('sitesecurity')->__('Title'),
    			'align' => 'left',
    			'index' => 'title'
    	));
    	
    	$this->addColumn('code', array(
    			'header' => Mage::helper('sitesecurity')->__('Code'),
    			'align' => 'left',
    			'index' => 'code'
    	));
    	
    	$this->addColumn('description', array(
    			'header' => Mage::helper('sitesecurity')->__('Description'),
    			'align' => 'left',
    			'index' => 'description'
    	));
        
        $this->addColumn('action', array(
        		'header' => Mage::helper('sitesecurity')->__('Manage (Edit or Delete)'),
        		'width' => '100',
        		'type' => 'action',
        		'getter' => 'getCatId',
        		'actions' => array(
        				array(
        						'caption' => Mage::helper('sitesecurity')->__('Edit'),
        						'url' => array('base' => '*/*/edit'),
        						'field' => 'cat_id'
        				),
        				array(
        						'caption' => Mage::helper('sitesecurity')->__('Delete'),
        						'url' => array('base' => '*/*/delete'),
        						'field' => 'cat_id'
        				)
        		),
        		'filter' => false,
        		'sortable' => false,
        		'index' => 'stores',
        		'is_system' => true,
        ));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
    	return $this->getUrl('*/*/edit', array('cat_id' => $row->getCatId()));
    }

}
