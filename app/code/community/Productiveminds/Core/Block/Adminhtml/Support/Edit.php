<?php

class Productiveminds_Core_Block_Adminhtml_Support_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {   
    
	public function __construct() {
    	parent::__construct ();
    
    	$this->_objectId = 'id';
    	$this->_controller = 'adminhtml_support';
    	$this->_blockGroup = 'productivemindscore';
    	$this->_removeButton ( 'back' );
    	$this->_removeButton ( 'reset' );
    	$this->_removeButton ( 'save' );
    	$this->_headerText = Mage::helper ('productivemindscore')->__ ('Productiveminds Support');
    }
 
    public function getHeaderText() {
        return Mage::helper('productivemindscore')->__('Productiveminds Support - Creating a Ticket');
    }
    
}