<?php

class Valkyrie_TopPromotion_Block_Adminhtml_TopPromotion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'toppromotion';
    $this->_controller = 'adminhtml_toppromotion';
    $this->_mode = 'edit';

    $this->_addButton('save_and_continue', array(
      'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class' => 'save',
    ), -100);
    $this->_updateButton('save', 'label', Mage::helper('toppromotion')->__('Save Promotion'));

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
    if (Mage::registry('toppromotion') && Mage::registry('toppromotion')->getId())
    {
      return Mage::helper('toppromotion')->__('Edit Promotion "%s"', $this->htmlEscape(Mage::registry('toppromotion')->getTitle()));
    } else {
      return Mage::helper('toppromotion')->__('New Promotion');
    }
  }

}