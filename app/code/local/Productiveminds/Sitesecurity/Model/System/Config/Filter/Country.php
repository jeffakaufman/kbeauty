<?php

class Productiveminds_Sitesecurity_Model_System_Config_Filter_Country {

    protected $_options = null;

    public function toOptionArray($localOptions = false, $flag = false) {
        if ($this->_options === null) {
            $this->_options = array();
            
            $countriesArray = Mage::getModel('directory/country')->getResourceCollection()->loadByStore()->toOptionArray($flag);
            
            $countries = array();
            if($localOptions) {
            	$this->_options['unk'] = 'Unknown Location';
            	$this->_options['loc'] = 'Visitor Browsed from Store Server';
            }
            foreach ($countriesArray as $aCountry) {
            	$this->_options[$aCountry['value']] = $aCountry['label'];
            }
        }
        return $this->_options;
    }

}
