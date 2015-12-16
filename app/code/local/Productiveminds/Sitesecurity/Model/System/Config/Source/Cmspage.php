<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Cmspage {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('cms/page')->getCollection() as $cmsPage) {
                if($cmsPage->getIsActive() == 1) {
                	$this->_options[] = array(
                			'value' => $cmsPage->getIdentifier(),
                			'label' => $cmsPage->getTitle(),
                	);
                }
            }
        }
        return $this->_options;
    }

}
