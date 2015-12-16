<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Renderer_Countrycat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{	
    public function render(Varien_Object $row) {
    	$catId = trim($row->getData($this->getColumn()->getIndex()));
    	$countryCat = Mage::getModel('sitesecurity/countrycat')->load($catId);
    	return $countryCat->getTitle();
    }
}