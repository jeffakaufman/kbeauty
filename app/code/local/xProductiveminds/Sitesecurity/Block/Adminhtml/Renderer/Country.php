<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        return $this->_getValue($row);
    }
	
	public function _getValue(Varien_Object $row) {
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        return Mage::app()->getLocale()->getCountryTranslation($val);
	}
}