<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
    	$store = Mage::app()->getStore($row->getData($this->getColumn()->getIndex()));
        return $store->getName() .' ('.$store->getCode().')';        
    }

}
