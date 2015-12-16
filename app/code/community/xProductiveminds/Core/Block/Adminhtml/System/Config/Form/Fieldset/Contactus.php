<?php

class Productiveminds_Core_Block_Adminhtml_System_Config_Form_Fieldset_Contactus
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= Mage::helper('productivemindscore')->__(Mage::getStoreConfig('productivemindscore_sectns/contactus/text'));
        $html .= $this->_getFooterHtml($element);
        return $html;
    }
}
