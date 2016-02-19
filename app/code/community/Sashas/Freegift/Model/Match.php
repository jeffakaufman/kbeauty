<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Freegift_Model_Match extends Mage_Core_Model_Abstract
{
	protected $_conditions;
	protected $_actions;
	protected $_entityAttributeValues;
	protected $_productIds;
	protected $_actionProductIds;
	
	public function setConditions($conditions){
		$this->_conditions=$conditions;
		return $this;
	}

	public function setActions($actions){
		$this->_actions=$actions;
		return $this;
	}
		
	public function GetproductIds(){
		return $this->_productIds;
	}
	
	public function getActionProductIds(){
		return $this->_actionProductIds;
	}
 
	/**
	 * @param  $productCollection
	 * @return Sashas_Freegift_Model_Match
	 */
	public function collectValidatedAttributes($productCollection, $rule_type)
	{		 		 
		if ($rule_type=='conditions') {
			foreach ($this->getConditions()->getConditions() as $condition) {
	 		$attribute = $condition->getAttribute();
				  
				if ('category_ids' != $attribute && 'total_qty' != $attribute && 'base_subtotal' != $attribute) {
					if ($condition->getAttributeObject()->isScopeGlobal()) {
						$attributes = $this->getRule()->getCollectedAttributes();  
						$attributes[$attribute] = true;
						$this->getRule()->setCollectedAttributes($attributes);
						$productCollection->addAttributeToSelect($attribute, 'left');		 
					} else {				 
							$this->_entityAttributeValues = $productCollection->getAllAttributeValues($attribute);							 
					}
				}					
			}	 
		} else {
			$this->getActions()->setRule($this->getRule())->collectValidatedAttributes($productCollection);			
		}
		return $this->getRule();
	}
	
	/**
	 * Retrieve giftrule  conditions model
	 */
	public function getConditions() {
		
		if (empty($this->_conditions)) {		 
			$conditions =  $this->getRule()->getConditionsInstance();		 
			$conditions->setId('1')->setPrefix('conditions');
			$this->_conditions=$conditions;		
		}
				 
		// Load rule conditions if it is applicable
		if ($this->getRule()->hasConditionsSerialized()) {
			$conditions = $this->getRule()->getConditionsSerialized();
			if (!empty($conditions)) {
					$conditions = unserialize($conditions);
					 
				if (is_array($conditions) && !empty($conditions)) {
					$this->_conditions->loadArray($conditions);
				}
			}		
			 $this->getRule()->setConditions($this->_conditions);
			 $this->getRule()->unsConditionsSerialized();
		}
		 
		return $this->_conditions;
	}
	
	/**
	 * Retrieve giftrule actions model
	 */
	public function getActions() {
	
		if (empty($this->_actions)) {
			$actions =  $this->getRule()->getActionsInstance();
			$actions->setId('1')->setPrefix('actions');
			$this->_actions=$actions;
		}
			 
		// Load rule conditions if it is applicable
		if ($this->getRule()->hasActionsSerialized()) {
			$actions = $this->getRule()->getActionsSerialized();
			if (!empty($actions)) {
				$actions = unserialize($actions);
				if (is_array($actions) && !empty($actions)) {
					$this->_actions->loadArray($actions);
				} 
			}
			 
			$this->getRule()->setActions($this->_actions);
			$this->getRule()->unsActionsSerialized();
		}
	 
		return $this->_actions;
	}
	
	/**
	 * Callback function for product matching
	 *
	 * @param $args
	 * @return void
	 */
	public function callbackValidateProduct($args)
	{
		$product = clone $args['product'];
		$product->setData($args['row']);
 
		if ($this->validate($product)) {   
			$this->_productIds[] = $product->getId();
		}	
	}
	
	
	public function callbackValidateActionProduct($args)
	{
		 
		$product = clone $args['product'];
		 
		$product->setData($args['row']);
		$attribute='sku';//$args['attributes']['sku'];
 
		if ($this->getActions()->setAttribute($attribute)->validate($product)) {
			$this->_actionProductIds[] = $product->getId();			 
		} 
	}
 
	public function validate (Varien_Object $object) {
		if (!$this->getConditions()) {
			return true;
		}
		 
		$all    = $this->getConditions()->getAggregator() === 'all';
		$true   = (bool)$this->getConditions()->getValue();		
		
		foreach ($this->getConditions()->getConditions() as $cond)  {
			 
			if ($cond->getAttribute()=='base_subtotal'  ||  $cond->getAttribute()=='total_qty' )
				$validated=true;			 
			else 
				$validated = $cond->validate($object);
			
			if ($all && $validated !== $true) {
				return false;
			} elseif (!$all && $validated === $true) {
				return true;
			}
		}
		return $all ? true : false;
	}
}