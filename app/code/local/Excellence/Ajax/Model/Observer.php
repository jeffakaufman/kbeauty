<?php

class Excellence_Ajax_Model_Observer
{

    public function addToProductAlert(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product->getIsSalable()) {// if out of stock
            //subscribe to stock alert
            $model = Mage::getModel('productalert/stock')
                ->setCustomerId(Mage::getSingleton('customer/session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
        }
    }

}