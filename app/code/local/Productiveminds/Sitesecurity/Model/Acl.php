<?php

class Productiveminds_Sitesecurity_Model_Acl extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('sitesecurity/acl');
	}

}