<?php

class Productiveminds_Sitesecurity_Model_Action extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('sitesecurity/action');
	}

}