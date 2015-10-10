<?php

class Valkyrie_SliderData_Block_Adminhtml_SliderData_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'sliderdata';
    $this->_controller = 'adminhtml_sliderdata';
    $this->_mode = 'edit';

    $this->_addButton('save_and_continue', array(
      'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class' => 'save',
    ), -100);
    $this->_updateButton('save', 'label', Mage::helper('sliderdata')->__('Save Slide'));

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
    if (Mage::registry('sliderdata') && Mage::registry('sliderdata')->getId())
    {
      return Mage::helper('sliderdata')->__('Edit Slide "%s"', $this->htmlEscape(Mage::registry('sliderdata')->getTitle()));
    } else {
      return Mage::helper('sliderdata')->__('New Slide');
    }
  }

}