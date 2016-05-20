<?php

class Productiveminds_Sitesecurity_Model_Mysql4_Countrycat extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('sitesecurity/countrycat', 'cat_id');
	}
}