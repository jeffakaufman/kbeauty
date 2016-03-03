<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Sitesecure_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset->addField('country', 'select', array(
        		'label'     => Mage::helper('sitesecurity')->__('Country'),
        		'class'     => 'required-entry',
        		'required'  => true,
        		'name'      => 'country',
        		'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
        		'value'     => $model->getCountry()
        ));
        
        /**
         * Store view
         */
        	$fieldset->addField('store_id', 'select', array(
        			'name' => 'store_id',
        			'label' => Mage::helper('sitesecurity')->__('Store View'),
        			'title' => Mage::helper('sitesecurity')->__('Store View'),
        			'required' => true,
        			'values' => Mage::getSingleton('sitesecurity/system_store')->getStoreValuesForForm(true, false),
        			'value'  => $model->getStoreId()
        	));
        
        $fieldset->addField('status', 'select', array(
        		'label'     => Mage::helper('sitesecurity')->__('Status'),
        		'required'  => true,
        		'name'      => 'status',
        		'value'  => $model->getStatus(),
        		'values'    => array(
        				array(
        						'value'     => 1,
        						'label'     => Mage::helper('sitesecurity')->__('Enabled'),
        				),
        				array(
        						'value'     => 2,
        						'label'     => Mage::helper('sitesecurity')->__('Disabled'),
        				),
        		),
        ));
        
        return parent::_prepareForm();
    }
}