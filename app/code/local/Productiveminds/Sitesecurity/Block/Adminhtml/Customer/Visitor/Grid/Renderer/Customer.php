<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$customerId = trim($row->getData($this->getColumn()->getIndex()));
    	if( $customerId == 0 ) {
    		return 'n/a';
    	} else {
    		return $customerId;
    	}       
    }

}
