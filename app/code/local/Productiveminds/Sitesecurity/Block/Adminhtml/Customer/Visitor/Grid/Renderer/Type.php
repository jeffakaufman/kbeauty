<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        return ($row->getCustomerId() > 0 ) ? Mage::helper('sitesecurity')->__('Customer') : Mage::helper('sitesecurity')->__('Customer') ;
    }

}
