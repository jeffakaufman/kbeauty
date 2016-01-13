<?php

class Aitoc_Aitreports_Block_Export_Edit_Tab_Entities extends Aitoc_Aitreports_Block_Export_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $this->setForm($form);

        $this->setTemplate('aitreports/export_entities.phtml');

        return parent::_prepareForm();
    }
}
