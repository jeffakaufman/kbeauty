<?php

class Productiveminds_Sitesecurity_Block_Sitesecure extends Productiveminds_Sitesecurity_Block_Abstract
{
    public function getItems() {
    	$collection = Mage::getModel('sitesecurity/sitesecure')->getCollection();
    	return $collection;
    }
    
}
