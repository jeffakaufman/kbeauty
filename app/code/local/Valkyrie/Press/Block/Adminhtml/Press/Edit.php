<?php

class Valkyrie_Press_Block_Adminhtml_Press_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'press';
    $this->_controller = 'adminhtml_press';
    $this->_mode = 'edit';

    $this->_addButton('save_and_continue', array(
      'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class' => 'save',
    ), -100);
    $this->_updateButton('save', 'label', Mage::helper('press')->__('Save Press'));

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
    if (Mage::registry('press') && Mage::registry('press')->getId())
    {
      return Mage::helper('press')->__('Edit Press "%s"', $this->htmlEscape(Mage::registry('press')->getImageName()));
    } else {
      return Mage::helper('press')->__('New Press');
    }
  }

}