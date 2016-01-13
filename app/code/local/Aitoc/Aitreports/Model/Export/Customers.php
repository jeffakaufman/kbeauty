<?php

class Aitoc_Aitreports_Model_Export_Customers extends Mage_Core_Model_Abstract
{
    const CACHE_ID = 'aitreports_customer_orders_data';

    /**
     * @var array
     */
    protected $_customers = null;

    /**
     * Load first customer orders from cache or from collection
     *
     * @return Aitoc_Aitreports_Model_Export_Customers
     */
    public function loadCustomersOrders()
    {
        $data = Mage::app()->loadCache(self::CACHE_ID);
        if($data) {
            $data = unserialize($data);
            if(is_array($data) && count($data)>0) {
                $this->_customers = $data;
                return $this;
            }
        }
        $data = $this->_getDataFromCollection();
        Mage::app()->saveCache(serialize($data), self::CACHE_ID, array(), 900);
        $this->_customers = $data;
        return $this;
    }

    /**
     * Validate if order is the first that this customer have purchased and set that flat in model
     *
     * @param Mage_Sales_Model_Order $order
     * @return Aitoc_Aitreports_Model_Export_Customers
     */
    public function checkEmail($order)
    {
        if(is_null($this->_customers)) {
            $this->loadCustomersOrders();
        }
        $order->setFirstOrder(0);
        if(isset($this->_customers[$order->getCustomerEmail()])) {
            if($order->getId() == $this->_customers[$order->getCustomerEmail()]) {
                $order->setFirstOrder(1);
            }
        }
        return $this;
    }

    /**
     * Select customers data from database
     *
     * @return array
     */
    protected function _getDataFromCollection()
    {
        if(version_compare(Mage::getVersion(),'1.4.1.1','>=')) {
            $collection = Mage::getModel('sales/order')->getCollection();
            $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array(
                    'min_id' => 'min(entity_id)',
                    'customer_email' => 'customer_email'
                ))
                ->group('customer_email');
        } else {
            $collection = Mage::getModel('sales/order')->getCollection();
            $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array(
                    'min_id' => 'min(e.entity_id)',
                    #'customer_email' => 'customer_email'
                ));
            $collection                
                ->joinAttribute('customer_email', 'order/customer_email', 'entity_id', null, 'inner'); 
            $collection->getSelect()                
                ->group('customer_email');
        }
        $data = array();
        foreach($collection as $order) {
            $data[$order->getCustomerEmail()] = $order->getMinId();
        }
        return $data;
    }

}
