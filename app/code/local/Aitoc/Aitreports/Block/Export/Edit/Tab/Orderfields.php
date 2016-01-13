<?php

class Aitoc_Aitreports_Block_Export_Edit_Tab_Orderfields extends Aitoc_Aitreports_Block_Export_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $this->setForm($form);
        
        $this->setTemplate('aitreports/orderfields.phtml');
        
        return parent::_prepareForm();
    }
    
    public function getOrderFields()
    {
        return Mage::getModel('aitreports/export_type_order')->getOrderFields();
    }
    
    public function getAttributeCodeEavFlat($field)
    {
        return Mage::getModel('aitreports/export_type_order')->getAttributeCodeEavFlat($field);
    }
}
