<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Model_Observer
{
	static protected $_singletonFlag = false;
	
	/** 
	 * @event checkout_cart_product_add_after
	 * @param Varien_Event_Observer $observer
	 */
	public function AddtoCart(Varien_Event_Observer $observer) {
		
		$quote_item=$observer->getQuoteItem();
	 	$product=$observer->getProduct();
	 	 
	 	/*If freegift added skip the rule*/
	 	if ($product->getIsFreegift()){
	 	    $quote_item->setIsFreegift(1)->setPrice(0)->save();	 	    	 	   
	 	    return $this;
	 	}
	 	/* New Validation Logic*/
	 	Mage::log('---Sashas_Freegift_Model_Observer::AddtoCart---', null, 'freegift.log');
	 	
	 	$observer->setAddP(1);
	 	$quote_item->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
	 	
	 	Mage::log('Product ID: '.$quote_item->getProductId(), null, 'freegift.log');
	 	$this->validateAllRules($observer);
	 	Mage::log('---Sashas_Freegift_Model_Observer::AddtoCart END---', null, 'freegift.log');
	    /* New Validation Logic*/
	 	return $this; 	    
	}
	
	/**
	 * @param Varien_Event_Observer $observer
	 * @return Sashas_Freegift_Model_Observer
	 */
	public function validateAllRules(Varien_Event_Observer $observer){
	    Mage::log('Sashas_Freegift_Model_Observer::validateAllRules', null, 'freegift.log');
	    
	    $customer_group=0;
	    if (Mage::helper('customer')->isLoggedIn())
	        $customer_group=Mage::helper('customer')->getCustomer()->getGroupId();	     
	    $website_id=Mage::app()->getStore()->getWebsiteId();
	    
		$session=Mage::getSingleton('checkout/session');
		$quote_id=$session->getQuoteId();
		if (!$quote_id){
		    Mage::log('Cart is not exist', null, 'freegift.log');
		    return $this;
		}
		/*Make sure it passed only when item removed*/		 
		if ($observer->getAddP())
			$addedQuoteItem=$observer->getQuoteItem();
		else
		    $deletedQuoteItem=$observer->getQuoteItem();
		
		$quote=Mage::getModel('sales/quote')->load($quote_id);
		$itemsInCart= $quote->getAllItems();
		$nonGiftCartItems=array();		 
		$cartHelper = Mage::helper('checkout/cart');
		if (count($itemsInCart)<1)
			Mage::log('No Items in the cart', null, 'freegift.log');
		 
		foreach ($itemsInCart as $quoteItem) { 
		    if($quoteItem->getIsFreegift()){		
		        Mage::log('Removed Item ID:'.$quoteItem->getId(), null, 'freegift.log');
		        $cartHelper->getCart()->removeItem($quoteItem->getId())->save();		
		         
		        continue;
		    }
		    /*Deleted Items Handle*/
		    if($deletedQuoteItem && $quoteItem->getId()==$deletedQuoteItem->getId()){		
		        Mage::log('Skipped Item ID:'.$deletedQuoteItem->getId(), null, 'freegift.log');
		        continue;
		    }
		    
		    $nonGiftCartItems[]=$quoteItem->getProductId();
		    Mage::log('Included into vlaidation product ID:'.$quoteItem->getProductId(), null, 'freegift.log');
		}
		if ($addedQuoteItem && is_object($addedQuoteItem)){
		        $nonGiftCartItems[]=$addedQuoteItem->getProductId();
				Mage::log('Included into vlaidation product ID:'.$addedQuoteItem->getProductId(), null, 'freegift.log');
		}
		 
		Varien_Profiler::start('FreegiftLoad_start');		
	    $collection=Mage::getModel('freegift/rule_apply')->getCollection()
	    	->AddFieldtoFilter('product_id',array('in'=>$nonGiftCartItems))
	    	->AddFieldtoFilter('customer_group_id',$customer_group)
	    	->AddFieldtoFilter('website_id',$website_id);
	    $collection->getSelect()->group('rule_id');
	    Varien_Profiler::start('FreegiftLoad_stop');
	    
	    $added_products_ids=array();
	    $added_products_ids=Mage::helper('freegift')->checkFreeGiftRules($collection);
	    
	     
	    $cartHelper = Mage::helper('checkout/cart');
	    foreach ($added_products_ids as $gift_product_id){	        
	        $_product=Mage::getModel('catalog/product')->load($gift_product_id);
	        $_product->setIsFreegift(1);
	        $qty=(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
	        if ($qty){
	            Mage::log('Added Free Gift: '.$gift_product_id, null, 'freegift.log');
	            $cartHelper->getCart()->addProduct($_product, array('qty' => 1))->save();
	        } else{
	            Mage::log('Stock issue - Free Gift ID: '.$gift_product_id, null, 'freegift.log');
	        }
	    }
 
	    Mage::log('Sashas_Freegift_Model_Observer::validateAllRules END', null, 'freegift.log');
	    return $this;
	}
 
 
	/**
	 * @event sales_quote_remove_item
	 * @param Varien_Event_Observer $observer
	 * @return Sashas_Freegift_Model_Observer
	 */
	public function RemovefromCart(Varien_Event_Observer $observer) {
	    
	    /* New Validation Logic */
	    Mage::log('---Sashas_Freegift_Model_Observer::RemovefromCart---', null, 'freegift.log');
	    $quote_item=$observer->getQuoteItem();
	    if ($quote_item->getIsFreegift())
	        return $this;	   
	    $this->validateAllRules($observer);
	    Mage::log('---Sashas_Freegift_Model_Observer::RemovefromCart END---', null, 'freegift.log');
	    return $this;
	    /* New Validation Logic */	    		 
	}
 
	/**
	 * @event checkout_cart_update_items_after
	 * @param Varien_Event_Observer $observer
	 * @return Sashas_Freegift_Model_Observer
	 */
	public function UpdateCartItem(Varien_Event_Observer $observer) {	    
	    /* New Validation Logic */
	    Mage::log('---Sashas_Freegift_Model_Observer::UpdateCartItem---', null, 'freegift.log');
	    $this->validateAllRules($observer);
	    Mage::log('---Sashas_Freegift_Model_Observer::UpdateCartItem END---', null, 'freegift.log');
		return $this; 
		/* New Validation Logic */   
	}
 
}