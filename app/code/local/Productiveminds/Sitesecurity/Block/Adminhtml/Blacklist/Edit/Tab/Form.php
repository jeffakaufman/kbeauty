<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Blacklist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset->addField('remote_addr', 'text', array(
        		'label'     => Mage::helper('review')->__('IP Address'),
        		'required'  => true,
        		'name'      => 'remote_addr',
        		'value'     => Mage::getModel('sitesecurity/security')->getLong2ip($model->getRemoteAddr())
        ));
        
        $fieldset->addField('status', 'select', array(
        		'label'     => Mage::helper('sitesecurity')->__('Status'),
        		'required'  => true,
        		'name'      => 'status',
        		'value'     => $model->getStatus(),
        		'values'    => Mage::getSingleton('sitesecurity/system_config_source_statusfield')->toOptionEnableArray(),
        ));
        
        return parent::_prepareForm();
    }
}