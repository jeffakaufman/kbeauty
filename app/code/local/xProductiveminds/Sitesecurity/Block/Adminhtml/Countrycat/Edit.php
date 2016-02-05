<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Countrycat_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sitesecurity';
        $this->_controller = 'adminhtml_countrycat';

        $this->_updateButton('delete', 'label', Mage::helper('sitesecurity')->__('Delete Item'));
        $this->_updateButton('save', 'label', Mage::helper('sitesecurity')->__('Save Item'));
        $this->_addButton('saveandcontinue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
        	function saveAndContinueEdit(){
        		editForm.submit($('edit_form').action+'back/edit/');
        	}
        ";
    }
 
    public function getHeaderText()
    {
        if (Mage::registry('sitesecurity_data') && Mage::registry('sitesecurity_data')->getCatId())
        {
            return Mage::helper('sitesecurity')->__('Edit Country Category: %s', Mage::registry('sitesecurity_data')->getTitle());
        } else {
            return Mage::helper('sitesecurity')->__('New Category');
        }
    }
    
}