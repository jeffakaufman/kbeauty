<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Acl {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('sitesecurity/acl')->getCollection() as $acl) {
            	if ($acl->getId() > 0) {
            		$this->_options[] = array(
            				'value' => $acl->getCode(),
            				'label' => $acl->getDescription(),
            		);
            	}
            }
        }
        return $this->_options;
    }

}
