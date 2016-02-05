<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Cmsblock {

    protected $_options = null;

    public function toOptionArray() {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getModel('cms/block')->getCollection() as $cmsBlock) {
                if( $cmsBlock->getIsActive() == 1 ) {
                	$this->_options[] = array(
                			'value' => $cmsBlock->getIdentifier(),
                			'label' => $cmsBlock->getTitle(),
                	);
                }
            }
        }
        return $this->_options;
    }

}
