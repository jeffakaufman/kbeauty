<?php

class Productiveminds_Sitesecurity_Model_Resource_Review extends Mage_Review_Model_Resource_Review
{
	const ADMINSTORECODE = 'admin';

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    	parent::_afterSave($object);
    	
    	$visitedStoreCode = Mage::app()->getStore()->getCode();
    	// if a frontend user
    	if ($visitedStoreCode != self::ADMINSTORECODE) {
    		// get IP address
    		$ipAddy = Mage::helper('core/http')->getRemoteAddr();
    		
    		$ipAddy = Mage::getModel('sitesecurity/security')->getIp2long($ipAddy);
    		 
    		$adapter = $this->_getWriteAdapter();
  
    		$detail = array();
    		$select = $adapter->select()
    		->from($this->_reviewDetailTable, 'detail_id')
    		->where('review_id = :review_id');
    		$detailId = $adapter->fetchOne($select, array(':review_id' => $object->getId()));
    		 
    		if ($detailId) {
    			$detail['pms_sitesecurity_ip']  = $ipAddy;
    			$condition = array("detail_id = ?" => $detailId);
    			$adapter->update($this->_reviewDetailTable, $detail, $condition);
    		}
    	}
        return $this;
    }

}
