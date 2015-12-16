<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Country_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('sitesecurity_form', array('legend' => Mage::helper('sitesecurity')->__('Optional information')));
        $fieldset->addField('description', 'textarea', array(
        		'label'     => Mage::helper('sitesecurity')->__('Description'),
        		'name'      => 'description',
        		'value'     => $model->getDescription(),
        		'note'     => Mage::helper('sitesecurity')->__('Description'),
        ));
        return parent::_prepareForm();
    }
}