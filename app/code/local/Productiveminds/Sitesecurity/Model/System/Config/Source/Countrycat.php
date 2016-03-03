<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Countrycat {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('sitesecurity/countrycat')->getCollection() as $aCountrycat) {
            	if ($aCountrycat->getCatId() > 0) {
            		$this->_options[] = array(
            				'value' => $aCountrycat->getCatId(),
            				'label' => $aCountrycat->getTitle(),
            		);
            		//$this->_options[$aCountrycat->getCatId()] = $aCountrycat->getTitle();
            	}
            }
        }
        return $this->_options;
    }

}
