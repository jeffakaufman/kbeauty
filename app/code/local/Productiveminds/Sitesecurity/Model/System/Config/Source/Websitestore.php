<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Websitestore
{
    protected $_options;
    
    public function toOptionArray()
    {
    	$_options = array();
		foreach ($this->getAllOptions() as $option) {
			$_options[$option['value']] = $option['label'];
		}
		return $_options;
    }
    
    public function getOptionArray()
    {
    	$_options = array();
    	foreach ($this->getAllOptions() as $option) {
    		$_options[$option['value']] = $option['label'];
    	}
    	return $_options;
    }
    
    public function getAllOptions()
    {
    	if (is_null($this->_options)) {
    		$stores = Mage::getResourceModel('core/store_collection');
    		$_options = array();
    		foreach ($stores as $store) {
    			$website = Mage::getModel('core/website')->load($store->getWebsiteId());
    			$_options[] = array(
    					//'label' => $website->getName().'('.$website->getCode().'). '.' => '.$store->getName(),
    					//'label' => $website->getCode(). ' => '.$store->getName().'('.$store->getCode().')',
    					'label' => $store->getName().' ('.$store->getCode().')',
    					//'value' =>  $website->getWebsiteId().'_'.$store->getStoreId()
    					'value' =>  $store->getId()
    				);
    		}
    	}
    	return $_options;
    }
    
}
