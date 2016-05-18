<?php

class Valkyrie_DailyFierce_Block_Adminhtml_DailyFierce_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'dailyfierce';
    $this->_controller = 'adminhtml_dailyfierce';
    $this->_mode = 'edit';

    $this->_addButton('save_and_continue', array(
      'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class' => 'save',
    ), -100);
    $this->_updateButton('save', 'label', Mage::helper('dailyfierce')->__('Save Daily Fierce'));

    $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
  }

  public function getHeaderText()
  {
    if (Mage::registry('dailyfierce') && Mage::registry('dailyfierce')->getId())
    {
      return Mage::helper('dailyfierce')->__('Edit Daily Fierce "%s"', $this->htmlEscape(Mage::registry('dailyfierce')->getTitle()));
    } else {
      return Mage::helper('dailyfierce')->__('New Daily Fierce');
    }
  }

}