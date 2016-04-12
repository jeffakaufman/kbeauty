<?php

class Valkyrie_PromotionModules_Block_Adminhtml_PromotionModules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'promotionmodules';
    $this->_controller = 'adminhtml_promotionmodules';
    $this->_mode = 'edit';

    $this->_addButton('save_and_continue', array(
      'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class' => 'save',
    ), -100);
    $this->_updateButton('save', 'label', Mage::helper('promotionmodules')->__('Save Module'));

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
    if (Mage::registry('promotionmodules') && Mage::registry('promotionmodules')->getId())
    {
      return Mage::helper('promotionmodules')->__('Edit Module "%s"', $this->htmlEscape(Mage::registry('promotionmodules')->getTitle()));
    } else {
      return Mage::helper('promotionmodules')->__('New Module');
    }
  }

}