<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Action {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('sitesecurity/action')->getCollection() as $action) {
                if ($action->getCode() != Productiveminds_Sitesecurity_Model_Security::ACTION_CODE_NONE) {
                	$this->_options[] = array(
                			'value' => $action->getCode(),
                			'label' => $action->getTitle(),
                	);
                }
            }
        }
        return $this->_options;
    }

}
