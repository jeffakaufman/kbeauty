<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Countrycat_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{    
	protected function _prepareForm()
    {
        if (Mage::registry('sitesecurity_data'))
        {
            $model = Mage::registry('sitesecurity_data');
        }
        else
        {
            $model = array();
        }
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('sitesecurity_form', array('legend' => Mage::helper('sitesecurity')->__('General information')));
        
        $title = trim($model->getTitle());
        $displayType = 'text';
        if($title && $title!= '') {
        	$displayType = 'label';
        }
        $fieldset->addField('title', 'text', array(
        		'label'     => Mage::helper('sitesecurity')->__('Title'),
        		'class'     => 'required-entry',
        		'required'  => true,
        		'name'      => 'title',
        		'value'     => $model->getTitle()
        ));
        
        $title = trim($model->getTitle());
        $displayType = 'text';
        if($title && $title!= '') {
        	$displayType = 'label';
        }
        $fieldset->addField('code', $displayType, array(
        		'label'     => Mage::helper('sitesecurity')->__('Code (e.g, sAmerica for South America)'),
        		'class'     => 'required-entry',
        		'required'  => true,
        		'name'      => 'code',
        		'value'     => $model->getCode()
        ));
        
        $fieldset->addField('description', 'textarea', array(
        		'label'     => Mage::helper('sitesecurity')->__('Description'),
        		'name'      => 'description',
        		'value'     => $model->getDescription(),
        		'note'     => Mage::helper('sitesecurity')->__('Description'),
        ));
        
        return parent::_prepareForm();
    }
}