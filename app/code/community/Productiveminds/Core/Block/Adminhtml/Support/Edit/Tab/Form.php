<?php

class Productiveminds_Core_Block_Adminhtml_Support_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm() {
		
		$adminId = Mage::getSingleton ( 'admin/session' )->getUser ()->getId ();
		$user = Mage::getModel ( 'admin/user' )->load ( $adminId );
		
		$form = new Varien_Data_Form ();
		$this->setForm ( $form );
		
		$helper = Mage::helper ( 'productivemindscore' );
		
		$fieldset = $form->addFieldset ( 'productivemindscore_form', array (
				'legend' => $helper->__ ( 'Support Tickets Details' ),
				'class' => 'fieldset-wide' 
		) );
		
		$fieldset->addField ( 'typeofissue', 'select', array (
				'name' => 'typeofissue',
				'label' => $helper->__ ( 'Type of issue' ),
				'values' => Mage::getModel ( 'productivemindscore/system_config_source_typeofissue' )->toOptionArray (),
				'required' => true,
				'value' => false 
		) );
		$fieldset->addField ( 'message', 'textarea', array (
				'name' => 'message',
				'label' => $helper->__ ( 'Issue Details' ),
				'required' => true,
				'note' => Mage::helper('productivemindscore')->__('Please provide as much details as possible'),
		) );
		
		$fieldset->addType ( 'attachedfiles', Mage::getConfig ()->getBlockClassName ('productivemindscore/adminhtml_renderer_attachedfiles') );
		
		$fieldset->addField ( 'attachments', 'attachedfiles', array (
				'name' => 'attachments[]',
				'multiple' => true,
				'label' => Mage::helper ( 'adminhtml' )->__ ( 'Attachment files' ) 
		) );
		
		$fieldset->addField ( 'useremail', 'text', array (
				'name' => 'useremail',
				'label' => Mage::helper ( 'adminhtml' )->__ ( 'Your e-mail' ),
				'value' => $user['email'],
				'required' => true 
		) );
		
		$fieldset->addField ( 'username', 'text', array (
				'name' => 'username',
				'label' => Mage::helper ( 'adminhtml' )->__ ( 'Your name' ),
				'value' => "{$user['firstname']} {$user['lastname']}",
				'required' => true 
		) );
		
		$fieldset->addField ( 'systeminfo', 'hidden', array (
				'name' => 'systeminfo',
				'value' => $helper->systemInfo () 
		) );
		
		$fieldset->addField ( 'save', 'submit', array (
				'name' => 'save',
				'class' => 'save',
				'value' => $helper->__ ( 'Submit Issue' ) 
		) );
		
		return parent::_prepareForm ();
	}
}