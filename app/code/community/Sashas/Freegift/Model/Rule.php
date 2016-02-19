<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Freegift_Model_Rule extends Mage_Rule_Model_Rule
{
	protected $_productsFilter = null;
	protected $_productIds;
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('freegift/rule');
		$this->setIdFieldName('rule_id');
	}
	
	public function getConditionsInstance()
	{
		return Mage::getModel('freegift/rule_condition_combine');
	}
	
	public function getActionsInstance()
	{
		return Mage::getModel('freegift/rule_action_product');
	}
	
	/**
	 * Filtering products that must be checked for matching with rule
	 *
	 * @param  int|array $productIds
	 */
	public function setProductsFilter($productIds)
	{
		$this->_productsFilter = $productIds;
	}
	
	/**
	 * Returns products filter
	 *
	 * @return array|int|null
	 */
	public function getProductsFilter()
	{
		return $this->_productsFilter;
	}
	
	/**
	 * Returns rule as an array for admin interface
	 *
	 * Output example:
	 * array(
	 *   'name'=>'Example rule',
	 *   'conditions'=>{condition_combine::toArray}
	 *   'actions'=>{action_collection::toArray}
	 * )
	 *
	 * @return array
	 */
	public function toArray(array $arrAttributes = array())
	{
		$out = parent::toArray($arrAttributes);
		return $out;
	}
	
	/**
	 * Get array of product ids which are matched by rule
	 *
	 * @return array
	 */
	public function getMatchingProductIds()
	{
		if (is_null($this->_productIds)) {
			$this->_productIds = array();
			$this->setCollectedAttributes(array());
			$websiteIds = $this->getWebsiteIds();  
			if (!is_array($websiteIds)) {
				$websiteIds = explode(',', $websiteIds);
			}
	
			if ($websiteIds) {
				$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addWebsiteFilter($websiteIds);
				if ($this->_productsFilter) {
					$productCollection->addIdFilter($this->_productsFilter);
				}
	 
				$match_model=Mage::getModel('freegift/match');				 
				$match_model->setRule($this)->collectValidatedAttributes($productCollection,'conditions');				 		 			
						
				Mage::getSingleton('core/resource_iterator')->walk(
						$productCollection->getSelect(),
						array(array($match_model, 'callbackValidateProduct')),
						array(
								'attributes' => $match_model->getRule()->getCollectedAttributes(),
								'product'    => Mage::getModel('catalog/product'),
						)
					 
				); 
			}
		}		 
		return $match_model->GetproductIds();
	}
	
	public function getCartConditions() {
		$cart_conditions=array();
		foreach ($this->getConditions()->getConditions() as $condition) {
			if ($condition->getType()=='freegift/rule_condition_address') {
				array_push($cart_conditions, array('attribute'=>$condition->getAttribute(), 'value'=>$condition->getValue(),'operator'=>$condition->getOperator()));	
				if ($condition->getAttribute()=='total_qty') 
					$this->setTotalQty(1);
				if ($condition->getAttribute()=='base_subtotal')
					$this->setSubtotal(1);
			}else {
				/*if ($condition->getOperator()=='!=' || $condition->getOperator()=='!{}' || $condition->getOperator()=='!()')
					array_push($cart_conditions, array('attribute'=>$condition->getAttribute(), 'value'=>$condition->getValue(),'operator'=>$condition->getOperator()));*/
				
				$this->setOtherConditions(1); //Set for condition where sku and subtotal/qty
			}					 
 		} 
 		return $cart_conditions;
	}
 
 	public function getActionProducts() {
 		$action_products=array();
 		$websiteIds =   Mage::getModel('core/website')->load( 'base', 'code')->getId();
 	
 		$productCollection = Mage::getResourceModel('catalog/product_collection');
 		//	->addWebsiteFilter($websiteIds); error if website code diff
 		$match_model=Mage::getModel('freegift/match');
 		$match_model->setRule($this)->collectValidatedAttributes($productCollection,'actions');
 		  
 		Mage::getSingleton('core/resource_iterator')->walk(
 				$productCollection->getSelect(),
 				array(array($match_model, 'callbackValidateActionProduct')), 
 				array(
 						'attributes' => $this->getCollectedActionAttributes(),
 						'product'    => Mage::getModel('catalog/product'),
 				) 		
 		); 		 		
 		return $match_model->getActionProductIds();
 	}
	
	/**
	 * Get array of assigned customer group ids
	 *
	 * @return array
	 */
	public function getCustomerGroupIds()
	{
		$ids = $this->getData('customer_group_ids');
		if (($ids && !$this->getCustomerGroupChecked()) || is_string($ids)) {
			if (is_string($ids)) {
				$ids = explode(',', $ids);
			}
	
			$groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
			$ids = array_intersect($ids, $groupIds);
			$this->setData('customer_group_ids', $ids);
			$this->setCustomerGroupChecked(true);
		}
		return $ids;
	}
	
	protected function _beforeSave()
	{
		parent::_beforeSave();
		if (is_array($this->getCustomerGroupIds())) {
			$this->setCustomerGroups(join(',', $this->getCustomerGroupIds()));
		}
		 
		$websiteIds = $this->_getData('website_ids');
		if (is_array($websiteIds)) {
			$this->setWebsiteIds(implode(',', $websiteIds));
		}
	}
	
	protected function _afterLoad()
	{
		parent::_afterLoad();
		$groupIds = $this->getCustomerGroups();
		if (is_string($groupIds)) {
			$this->setCustomerGroupIds(explode(',', $groupIds));
		}
		$websiteIds = $this->_getData('website_ids');
		if (is_string($websiteIds)) {
			$this->setWebsiteIds(explode(',', $websiteIds));
		}
	}
	
	/**
	 * Apply gift rule
	 */
	public function apply()
	{ 
		$this->_getResource()->updategiftRuleProductData($this);
	}
	
	/**
	 * Apply all gift rules
	 */
	public function applyAll()
	{
		$collection=$this->getCollection()->addFieldToFilter('is_active',1);
		$collection->walk(array($this->_getResource(), 'updategiftRuleProductData'));		 		 
	}
 
	
}