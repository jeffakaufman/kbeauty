<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Freegift_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct()
    {
        $this->_init('freegift/rule', 'rule_id');
    }
 
 
    /**
     * Update products which are matched for gift rule
     *
     * @param Sashas_Freegift_Model_Rule $rule
     * @return Sashas_Freegift_Model_Resource_Rule
     */
    public function updategiftRuleProductData(Sashas_Freegift_Model_Rule $rule)
    {
    	
    	$ruleId = $rule->getId();
    
    	$write = $this->_getWriteAdapter();
    	$read=$this->_getReadadapter();
    	$write->beginTransaction();
    
    	$write->delete($this->getTable('freegift/rule_apply'), $write->quoteInto('rule_id=?', $ruleId));
    
    	if (!$rule->getIsActive()) {
    		$write->commit();
    		return $this;
    	}
    
    	$websiteIds = $rule->getWebsiteIds();
    	if (!is_array($websiteIds)) {
    		$websiteIds = explode(',', $websiteIds);
    	}
    	if (empty($websiteIds)) {
    		return $this;
    	}
    
    	Varien_Profiler::start('__MATCH_GIFT_PRODUCTS__');
    	$productIds = $rule->getMatchingProductIds();            	         	
    	Varien_Profiler::stop('__MATCH_GIFT_PRODUCTS__');
    	$customerGroups = $rule->getCustomerGroups();
    	if (!is_array($customerGroups))
    		$customerGroups=explode(',',$customerGroups);
    	 
    	
    	$cart_options=array();
    	$cart_options['all']=$rule->getConditions()->getAggregator();
    	$cart_options['true']=(bool)$rule->getConditions()->getValue();
    	$cart_options['rules']=$rule->getCartConditions();    	    	    	     
    	$other_cond=$rule->getOtherConditions();
    	if (!$other_cond)
    		$other_cond=0;
    	$cart_options['other']=$other_cond;
    	
    	$cart_options=serialize($cart_options);
    	
    	$subtotal=$rule->getSubtotal();
    	$total_qty=$rule->getTotalQty();
    	if (!$subtotal)
    		$subtotal=0;
    	if (!$total_qty)
    		$total_qty=0;
     
       	$action_product_array=$rule->getActionProducts();
       	$action_product=implode(',',$action_product_array); //extra func
  
    	$rows = array();
    	
     
    	try {
    		foreach ($productIds as $productId) { 
    			if (in_array($productId,$action_product_array))
    				continue; // gift product only in action.
    			
    			$gift_products = $read->query('SELECT COUNT(*) as total FROM '.$this->getTable('freegift/rule_apply').' WHERE gift_product_ids ='.$productId);  
    			$gift_products =$gift_products->fetch();
    			if($gift_products['total']>0)
    				continue; // gift prodcuts used only for gifts
    			
    			foreach ($websiteIds as $websiteId) {  
    				foreach ($customerGroups as $customerGroupId) {  
    					$rows[] = array(
    							'rule_id'           => $ruleId,    						
    							'website_id'        => $websiteId,
    							'customer_group_id' => $customerGroupId,
    							'product_id'        => $productId,
    							'gift_product_ids'  => $action_product,	
    							'cart_serialized'	=>$cart_options,
    								
    					);
 
    					if (count($rows) == 1000) {
    						$write->insertMultiple($this->getTable('freegift/rule_apply'), $rows);
    						$rows = array();
    					}
    				}
    			}
    		}   
    		    	    
    		if (   count($productIds)==0 && ($subtotal || $total_qty)  ) {
    			foreach ($websiteIds as $websiteId) {  
    				foreach ($customerGroups as $customerGroupId) {  
    					$rows[] = array(
    							'rule_id'           => $ruleId,    						
    							'website_id'        => $websiteId,
    							'customer_group_id' => $customerGroupId,
    							'product_id'        => '0',
    							'gift_product_ids'  => $action_product,	
    							'cart_serialized'	=> $cart_options,
    									
    					);
 
    					if (count($rows) == 1000) {
    						$write->insertMultiple($this->getTable('freegift/rule_apply'), $rows);
    						$rows = array();
    					}
    				}
    			}
    		}
    		
    		if (!empty($rows)) {
    			$write->insertMultiple($this->getTable('freegift/rule_apply'), $rows);
    		}
    		$write->commit();
    	} catch (Exception $e) {
    		$write->rollback();
    		throw $e;
    	}
    
    	return $this;
    }
}