<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Freegift_Model_Rule_Apply extends Mage_Core_Model_Abstract {
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('freegift/rule_apply');
	}
	
	public function DeleteByRuleId($rule_id) {
		$collection=$this->getCollection()->addFieldToFilter('rule_id ',array('eq'=>$rule_id));
		 
		foreach ($collection as $applied_rule) {
			$applied_rule->delete();
		}
	}
}