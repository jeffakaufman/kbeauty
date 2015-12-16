<?php

class Productiveminds_Sitesecurity_Model_System_Config_Filter_Acl {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('sitesecurity/acl')->getCollection() as $acl) {
            	if ($acl->getId() > 1) {
            		$this->_options[$acl->getCode()] = $acl->getDescription();
            	}
            }
        }
        return $this->_options;
    }

}