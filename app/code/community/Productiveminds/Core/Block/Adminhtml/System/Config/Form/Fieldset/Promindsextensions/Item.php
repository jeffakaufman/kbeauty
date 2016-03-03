<?php

class Productiveminds_Core_Block_Adminhtml_System_Config_Form_Fieldset_Promindsextensions_Item extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
    
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $modules = Mage::getConfig()->getNode('modules')->children();
        $linkTitle = Mage::helper('productivemindscore')->__('Visit Extension');
        foreach ($modules as $moduleName => $values) {
            if (0 !== strpos($moduleName, 'Productiveminds_')) {
                continue;
            }

            $field = $element->addField($moduleName, 'label', array(
            		'label' => $moduleName,
            		'value' => (string) $values->version
            ));
            $html .= $field->toHtml();
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}
