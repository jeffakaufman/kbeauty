<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_CouponGift
 * @copyright   Copyright (c) 2014 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_CouponGift_Model_Observer
{
	static protected $_singletonFlag = false;
	const COUPON_GIFT_CODE = 'coupon_gift';
	
	public function SalesRulePrepareForm(Varien_Event_Observer $observer) {
		$form =$observer->getForm();	
		 
		$field = $form->getElement('simple_action');
		$options = $field->getValues();
		
		$options[] = array(
				'value' => self::COUPON_GIFT_CODE,
				'label' => 'Add Gift Product'
		);
	 
		$field->setValues($options);
				
		$after_element_js="
		<script type=\"text/javascript\" >
		document.getElementById('rule_simple_action').addEventListener('change', function(){couponGiftFields();}, false);
		function couponGiftFields () {			  
			if ($('rule_simple_action').value=='".self::COUPON_GIFT_CODE."') {		  
				$('rule_discount_amount').value=0;				
				if ($$('#rule_action_fieldset tr')[0]!=undefined) {  
					$$('#rule_action_fieldset tr').each(function(tr_el) {
						if  ($(tr_el).down('#rule_gift_product_sku')!=undefined || $(tr_el).down('#rule_gift_product_force_price')!=undefined  )						 
							$(tr_el).show();
						else
							$(tr_el).hide();
							
					if ($(tr_el).down('#rule_simple_action')!=undefined || $(tr_el).down('#rule_discount_amount')!=undefined  )
						$(tr_el).show();							
					});	  
				}				
			} else {
				if ($$('#rule_action_fieldset tr')[0]!=undefined) {  
					$$('#rule_action_fieldset tr').each(function(tr_el) {
						if  ($(tr_el).down('#rule_gift_product_sku')!=undefined || $(tr_el).down('#rule_gift_product_force_price')!=undefined  )						 
							$(tr_el).hide();
						else
							$(tr_el).show();
							
					if ($(tr_el).down('#rule_simple_action')!=undefined || $(tr_el).down('#rule_discount_amount')!=undefined  )
						$(tr_el).show();							
					});	  
				}				 				 									
			}
		}
		document.observe('dom:loaded', function() {  couponGiftFields(); });		 
		</script>";
		$field->setAfterElementHtml($after_element_js);
		
		$fieldset = $form->getElement('action_fieldset');
		
		$fieldset->addField('gift_product_sku', 'text', array(
			'name' => 'gift_product_sku',
			'label' => Mage::helper('coupongift')->__('Gift Product SKU'),				
		)
		);

		$fieldset->addField('gift_product_force_price', 'select', array(
			'name' => 'gift_product_force_price',
			'label' => Mage::helper('cms')->__('Set gift product price 0'),			
			'options'    => array(1=>"Yes", 0=>"No")
		)
		);		
		
		 return $this;
	}
	 
	public function cartCheck(Varien_Event_Observer $observer){
	 
		if (Mage::getSingleton('checkout/session')->getQuote() && Mage::getSingleton('checkout/session')->getQuote()->getId()) {

			$quote=Mage::getSingleton('checkout/cart')->getQuote();
			$cartHelper = Mage::helper('checkout/cart');
			
			$appliedRuleIds = Mage::getSingleton('checkout/session')->getQuote()->getAppliedRuleIds();
			$appliedRuleIds = explode(',', $appliedRuleIds);

			// if ($quote->getAppliedRuleIds()){				
			// 	return;
			// }

			$remove_flag = 1;
			foreach ($appliedRuleIds as $rule_id) {
				$rule = Mage::getModel('salesrule/coupon')->load($rule_id);				
				// if ($rule_id == '2'){ //Gift Product Test Rule ID = 2
				// 	$remove_flag = 0;
				// }
				if($rule->getSimpleAction()==self::COUPON_GIFT_CODE){
					$remove_flag = 0;
				}
			}
			
			if ($remove_flag == 1){
				foreach ($quote->getAllItems() as $quoteItem){				
					if ($quoteItem->getIsCoupongift()){
						 
						$quoteItem->setQty(0);
						$quoteItem->isDeleted(true);
						$quoteItem->save();
						$quote->removeItem($quoteItem->getId());					
						$quote->save();
						$cartHelper->getCart()->removeItem($quoteItem->getId())->save();
					}
				}
			}
		}			 
	}
	
	public function SalesRuleGiftValidator(Varien_Event_Observer $observer) {
		 
		$rule=$observer->getRule(); 	 
		if ($rule->getSimpleAction()!=self::COUPON_GIFT_CODE) 
			return $this;
	 
		$force_price=$rule->getGiftProductForcePrice();
		$gift_product_sku=$rule->getGiftProductSku();
		$quoteObj = Mage::getModel('checkout/cart')->getQuote();
		$product_id=Mage::getModel('catalog/product')->getIdBySku($gift_product_sku);
		$cart_obj = Mage::getModel('checkout/cart');		
		$delete_gift_product=0;
		$was_added=false;
		 
		if (!$product_id) {
			Mage::getSingleton('checkout/session')->addError(
					Mage::helper('coupongift')->__('Gift Product SKU "%s" Not Found.', Mage::helper('core')->htmlEscape($gift_product_sku))
			);
			/* Mage::throwException(Mage::helper('coupongift')->__('Gift Product SKU Not Found.')); */
			return $this;
		}
		 
		foreach ($cart_obj->getItems() as $quote_item) {
			if($quote_item->getProductId()==$product_id){
				$was_added=true;
				$gift_quote_item=$quote_item;
			}
		}		
		
		/* Check if original product was deleted */
		if (count($cart_obj->getItems())<2 && $gift_quote_item instanceof Mage_Sales_Model_Quote_Item) 
			$delete_gift_product=1;		 	
		
		 
		if ($was_added && !$delete_gift_product)
			return $this;
		 
		$_product=Mage::getModel('catalog/product')->load($product_id);		
		/* Check if original product was deleted */
		if ($delete_gift_product) { 			
			$quoteObj->removeItem($gift_quote_item->getId());
			return $this;			
		}
		 
		/* Check if original product was deleted */
		$quoteItem = Mage::getModel('sales/quote_item')->setProduct($_product);
		
		 
		
		/* Optional */
		if ($force_price)
			$quoteItem->setOriginalCustomPrice(0);
		else
			$quoteItem->setOriginalCustomPrice();
		/* Optional */		
		$quoteItem->setQty(1);
		$quoteItem->setIsCoupongift(1);
		$quoteObj->addItem($quoteItem);
		$quoteObj->save();
		
		return $this;
	}
	
	public function RemoveCoupon(Varien_Event_Observer $observer) {
		 
		if (Mage::app()->getRequest()->getModuleName()!=='checkout')
			return $this;

	     /*Validate*/
	    if ( Mage::app()->getRequest()->getParam('remove') != 1) {
		    $quote=$observer->getQuote();
		    $quote_id=$quote->getEntityId();
		    $obj= new Varien_Event_Observer;
		    $applied_coupon_id=Mage::getModel('sales/quote')->load($quote_id)->getAppliedRuleIds();
		    
		    $applied_coupon_ids_arr=explode(',',$applied_coupon_id);
		    foreach ($applied_coupon_ids_arr as $apr) {
		        $rule=Mage::getModel('salesrule/rule')->load($apr);
		    
		        if ($rule->getSimpleAction()!='coupon_gift')
		            continue;
		    }
		    
		    if ($rule->getSimpleAction()!='coupon_gift')
		        return $this;
		    
		   /* $obj->setRule($rule); 
		    $this->SalesRuleGiftValidator($obj);*/
		   $applied_coupon_id=Mage::getModel('sales/quote')->load($quote_id)->getAppliedRuleIds();
		        if (!$applied_coupon_id)
		        	return $this;
		        		
		    $gift_product_sku=$rule->getGiftProductSku();
		    $product_id=Mage::getModel('catalog/product')->getIdBySku($gift_product_sku);
		    foreach ($quote->getAllItems() as $quote_item) {
		        if($quote_item->getProductId()==$product_id){
		            $was_added=true;
		            $gift_quote_item=$quote_item;
		        }
		    }
		    
		    /* Check if original product was deleted */
		    if (count($quote->getAllItems())<2 && $gift_quote_item instanceof Mage_Sales_Model_Quote_Item)
		        $delete_gift_product=1;
		    if ($delete_gift_product) {
		        $quote->removeItem($gift_quote_item->getId());
		        return $this;
		    }
	    }
	    /*Validate*/
	    
		if ( Mage::app()->getRequest()->getParam('remove') != 1) 
			return $this;
		$quote=$observer->getQuote();
		$quote_id=$quote->getEntityId();  
		$applied_coupon_id=Mage::getModel('sales/quote')->load($quote_id)->getAppliedRuleIds();	
		if (!$applied_coupon_id)
			return $this;
		 
		$rule=Mage::getModel('salesrule/rule')->load($applied_coupon_id);
		if ($rule->getSimpleAction()!='coupon_gift')
			return $this;
		
		$gift_product_sku=$rule->getGiftProductSku();		
		$product_id=Mage::getModel('catalog/product')->getIdBySku($gift_product_sku);
		$cart_obj = Mage::getModel('checkout/cart');
		
		$gift_product_item_id='';
		foreach ($cart_obj->getItems() as $quote_cart_item) {
			if( $quote_cart_item->getProductId()==$product_id){
				$gift_product_item=$quote_cart_item;
				break;
			}
		}		
	 
		if (!$gift_product_item instanceof Mage_Sales_Model_Quote_Item)
			return $this;		
		
		$quote->removeItem($gift_product_item->getId());
		
		 
		 
		return $this;
	}
	
	public function UpdateCartItem (Varien_Event_Observer $observer) {   
                //$cookie_name = "cart_time";
                //$cookie_value = time();
                //setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
		$new_info=$observer->getInfo();
		$cart=$observer->getCart();
		 
		$quoteObj = Mage::getModel('checkout/cart')->getQuote();
		$applied_coupon_id=Mage::getModel('sales/quote')->load($quoteObj->getEntityId())->getAppliedRuleIds();
		if (!$applied_coupon_id)
			return $this;
		  
		$applied_coupon_ids_arr=explode(',',$applied_coupon_id);
		foreach ($applied_coupon_ids_arr as $apr) {
		    $rule=Mage::getModel('salesrule/rule')->load($apr);
		    
		    if ($rule->getSimpleAction()!='coupon_gift')
		        continue;
		}
			 
		if ($rule->getSimpleAction()!='coupon_gift')
			return $this;
		 
		$gift_product_sku=$rule->getGiftProductSku();
		$product_id=Mage::getModel('catalog/product')->getIdBySku($gift_product_sku);
				 				
		foreach ($observer->getCart()->getItems() as $quote_item) {
			if( $quote_item->getProductId()==$product_id){ 
				$gift_product_item=$quote_item;
				//break;
				/*Force qty*/
				 
				if ($new_info[$gift_product_item->getId()]['qty']>1)
					$gift_product_item->setQty(1)->save();
				/*Force qty*/
				 
			}
		}	
		 
		//die('remove');
		/*remove if qty changed*/ 
		$quoteItemBack=clone $gift_product_item;
		 
		$quoteObj->removeItem($gift_product_item->getId());
		unset($new_info[$gift_product_item->getId()]);
		$quoteObj->setTotalsCollectedFlag(false)->collectTotals()->save();
		
		if ($cart->getQuote()->getAppliedRuleIds()) {
			/* Add Item back if rule validated */ 		
			$quoteItemBack->setIsCoupongift(1);
			$quoteObj->addItem($quoteItemBack);
			$quoteObj->setTotalsCollectedFlag(false)->collectTotals()->save(); 
		}	 
		/*remove if qty changed*/				

		return $this;
	}
	
	public function RemovefromCart(Varien_Event_Observer $observer) {
                $cookie_name = "cart_time";
                $cookie_value = time();
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
	    $removed_quote_item=$observer->getQuoteItem();
	    $removed_product_id=$removed_quote_item->getProductId();
	    $cart=Mage::getModel('checkout/cart');
	    
	    $quoteObj = Mage::getModel('checkout/cart')->getQuote();
	    $applied_coupon_id=Mage::getModel('sales/quote')->load($quoteObj->getEntityId())->getAppliedRuleIds();
	
	    if (!$applied_coupon_id)
	        return $this;
	     
	    $rule=Mage::getModel('salesrule/rule')->load($applied_coupon_id);
	    if ($rule->getSimpleAction()!='coupon_gift')
	        return $this;
	     
	    $gift_product_sku=$rule->getGiftProductSku();
	    $product_id=Mage::getModel('catalog/product')->getIdBySku($gift_product_sku);
	
	    if ($removed_product_id==$product_id)
	        return $this;
	     
	    $quoteObj->setTotalsCollectedFlag(false)->collectTotals()->save();
	    if ($cart->getQuote()->getAppliedRuleIds()) {
	        return $this;
	    } else {
	        foreach ( $quoteObj->getAllItems() as $quote_item) {
	            if( $quote_item->getProductId()==$product_id){
	                 
	                $quote_item->isDeleted(true);
	                $quoteObj->removeItem($quote_item->getId())->save();
	                $quoteObj->save();
	                break;
	            }
	        }
	    }
	
	    return $this;
	     
	}
	 
}
