<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid_Renderer_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return long2ip($row->getData($this->getColumn()->getIndex()));
    }

}
