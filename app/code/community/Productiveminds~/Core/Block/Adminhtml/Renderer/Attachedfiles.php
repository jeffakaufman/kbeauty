<?php

class Productiveminds_Core_Block_Adminhtml_Renderer_Attachedfiles extends Varien_Data_Form_Element_File {
    public function getElementHtml()
    {    
        $html = parent::getElementHtml();
        $newntml = substr_replace($html, 'multiple="true"', -2, 0);
        return $newntml;
    }
}