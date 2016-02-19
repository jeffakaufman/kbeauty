<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */


class Sashas_Freegift_Block_Adminhtml_Freegift_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_freegift';
        $this->_blockGroup = 'freegift';
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('freegift')->__('Save Gift Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('freegift')->__('Delete Gift Rule'));

        $rule = Mage::registry('current_freegift_rule');
                
        $this->_addButton('save_apply', array(
        	'class'=>'save',
        	'label'=>Mage::helper('freegift')->__('Save and Apply'),
        	'onclick'=>"$('rule_auto_apply').value=1; editForm.submit()",
        ));
        $this->_addButton('save_and_continue', array(
        	'label'     => Mage::helper('freegift')->__('Save and Continue Edit'),
        	'onclick'   => 'saveAndContinueEdit();',
        	'class' => 'save'
        ), 10);
        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/'); } ";
      
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_freegift_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('freegift')->__("Edit Gift Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('freegift')->__('New Gift Rule');
        }
    }

}
