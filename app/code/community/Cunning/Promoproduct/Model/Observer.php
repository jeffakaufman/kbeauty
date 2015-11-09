<?php
/**
 * @category   Event Observer
 * @package    Cunning_Promoproduct
 * @author     Cunning (jaydeep.cunning@gmail.com)
 * @Created At  2014-11-05
 */
class Cunning_Promoproduct_Model_Observer
{	
	function sales_quote_collect_totals_after()
	{
		$enablePromoproduct = Mage::getStoreConfig('promoproduct_section/promoproduct_group/promoproduct_enable');
		$cartGrandTotal = Mage::getStoreConfig('promoproduct_section/promoproduct_group/promoproduct_cart_sub_total');
		$allowedCustomerGroup = explode(',',Mage::getStoreConfig('promoproduct_section/promoproduct_group/apply_customer_group'));
		$ids = explode(',',Mage::getStoreConfig('promoproduct_section/promoproduct_group/promoproduct_product_ids'));
		if($enablePromoproduct && $cartGrandTotal != '' && !empty($allowedCustomerGroup))
		{
			$sessionCustomer = Mage::getSingleton("customer/session");
			$customerGroupId = $sessionCustomer->getCustomerGroupId();
			
			if(in_array($customerGroupId,$allowedCustomerGroup))
			{
				$ids = array_combine($ids, $ids);
				$qty = Mage::getStoreConfig('promoproduct_section/promoproduct_group/promoproduct_product_qty');
				if($qty == '')
				{
					$qty = 1;
				}
				
				$cartTotal = Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
				if($cartTotal >= $cartGrandTotal)
				{
					$cartHelper = Mage::helper('checkout/cart');
					$items = $cartHelper->getCart()->getItems();
					foreach($items as $item)
					{
						if(in_array($item->getProduct()->getId(),$ids))
						{			
							unset($ids[$item->getProduct()->getId()]);
						}
					}						
					if(!empty($ids))
					{
						foreach($ids as $id)
						{
							$this->addPromoProduct($id, $qty);							
						}
					}
				}
				else
				{				
					$this->deletePromoProducts($ids);
				}
			}
			else
			{	
				$this->deletePromoProducts($ids);
			}
		}
	}
	
	public function addPromoProduct($id, $qty)
	{
		$_product = Mage::getModel('catalog/product')->load($id);
		$stockItem = $_product->getStockItem();
		if($stockItem->getIsInStock())
		{
		
			$cart = Mage::getSingleton('checkout/cart');
			//$cart->init();
			$cart->addProduct($_product, array('qty' => $qty));
			//$cart->save();
			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
			
			$quote = Mage::getModel('checkout/cart')->getQuote();
			$item = $quote->getItemByProduct($_product);
			
			$item->setCustomPrice(0);
			$item->setOriginalCustomPrice(0);
			$item->getProduct()->setIsSuperMode(true);
			$quote->save();
			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
				
			$successMessage = Mage::helper('promoproduct')->__('Free gift "%s" was added to your shopping cart.',$_product->getName());
			Mage::getSingleton('checkout/session')->addSuccess($successMessage);
			//Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"))->sendResponse();
			//exit;
		}
	}
	public function deletePromoProducts($ids)
	{
		if(!empty($ids))
		{
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
			foreach($items as $item)
			{
				if(in_array($item->getProduct()->getId(),$ids) && $item->getCustomPrice() == '0')
				{
					$itemId = $item->getItemId();
					$cartHelper->getCart()->removeItem($itemId);//->save();
				}
			}
		}
	}
	
	public function checkout_cart_add_before($observer)
	{		
		$observerData = $observer->getEvent()->getData();
		$controllerAction = $observerData['controller_action'];
		$productParams = $controllerAction->getRequest()->getParams();
		if(!empty($productParams) && $productParams['product'] != '')
		{
			$newProductId = $productParams['product'];	
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
			foreach($items as $item)
			{
				$cartProductId = $item->getProduct()->getId();
				if($cartProductId == $newProductId && $item->getCustomPrice() == '0')
				{
					$_product = Mage::getModel('catalog/product')->load($cartProductId);
					$successMessage = Mage::helper('promoproduct')->__('Free gift "%s" is already in your shopping cart.',$_product->getName());
					Mage::getSingleton('checkout/session')->addNotice($successMessage);
					Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"))->sendResponse();
					exit;
				}
			}			
		}
	}
}
?>