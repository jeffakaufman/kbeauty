<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Country_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
		
        $countryName = Mage::app()->getLocale()->getCountryTranslation($model->getCountry());
        $displayType = 'text';
        if($countryName && $countryName!= '') {
        	$displayType = 'label';
        }
        $fieldset->addField('country', $displayType, array(
        		'name'		=> 'code',
        		'label'		=> Mage::helper('sitesecurity')->__('Country'),
        		'title'		=> Mage::helper('sitesecurity')->__('Country'),
        		'required'	=> false,
        		'value'  	=> $countryName
        ));
        
        $fieldset->addField('cat_id', 'select', array(
        		'name' 		=> 'cat_id',
        		'label' 	=> Mage::helper('sitesecurity')->__('Continent/Group'),
        		'title' 	=> Mage::helper('sitesecurity')->__('Continent/Group'),
        		'required' 	=> true,
        		'values' 	=> Mage::getSingleton('sitesecurity/system_config_source_countrycat')->toOptionArray(),
        		'value'  	=> $model->getCatId()
        ));
        
        $fieldset->addField('status', 'select', array(
        		'label'     => Mage::helper('sitesecurity')->__('Status'),
        		'title'		=> Mage::helper('sitesecurity')->__('Status'),
        		'required'  => true,
        		'name'      => 'status',
        		'values'    => Mage::getSingleton('sitesecurity/system_config_source_statusfield')->toOptionArray(),
        		'value'		=> $model->getStatus(),
        ));
        return parent::_prepareForm();
    }
}