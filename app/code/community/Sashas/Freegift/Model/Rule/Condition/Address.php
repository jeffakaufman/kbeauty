<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Model_Rule_Condition_Address extends Mage_SalesRule_Model_Rule_Condition_Address {
	
	
	public function __construct()
	{
		parent::__construct();
		$this->setType('freegift/rule_condition_address');
	
	}
	/**
	 * Default operator input by type map getter
	 *
	 * @return array
	 */
	public function getDefaultOperatorInputByType()
	{
		if (null === $this->_defaultOperatorInputByType) {  
			parent::getDefaultOperatorInputByType();
			$this->_defaultOperatorInputByType['numeric'] = array('==',  '>', '<');					
		}
		return $this->_defaultOperatorInputByType;
	}
}