<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Grid_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$ipAddy = long2ip(trim($row->getData($this->getColumn()->getIndex())));
    	$visitorCountryCode = Mage::helper('sitesecurity/sitesecurity')->getGeoIpCountryId($ipAddy);
    	if( $ipAddy == '127.0.0.1' ) {
    		return 'Visitor Browsed from Store Server';
    	} else if(!empty($visitorCountryCode) && $visitorCountryCode != '') {
    		$countryName = Mage::getModel('directory/country')->loadByCode($visitorCountryCode);
    		return $countryName;
    	} else {
    		return 'Unknown';
    	}       
    }

}
