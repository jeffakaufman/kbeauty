<?php

class Productiveminds_Sitesecurity_Model_System_Config_Filter_Countrycat {

    protected $_options = null;

    public function toOptionArray($flag = false) {
        if ($this->_options === null) {
            $this->_options = array();
            $collection = Mage::getModel('sitesecurity/countrycat')->getCollection()->setOrder('title', 'ASC');
            
            if ($flag) {
            	$this->_options[''] = Mage::helper('core')->__('-- Please Select --');
            }
            foreach ($collection as $aCountrycat) {
            	if ($aCountrycat->getCatId() > 0) {
            		$this->_options[$aCountrycat->getCatId()] = $aCountrycat->getTitle();
            	}
            }
        }
        return $this->_options;
    }

}
