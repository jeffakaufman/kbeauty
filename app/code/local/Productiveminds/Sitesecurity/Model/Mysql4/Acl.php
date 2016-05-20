<?php

class Productiveminds_Sitesecurity_Model_Mysql4_Acl extends Mage_Core_Model_Mysql4_Abstract 
{
	public function _construct() {
		$this->_init('sitesecurity/acl', 'id');
	}
	
}