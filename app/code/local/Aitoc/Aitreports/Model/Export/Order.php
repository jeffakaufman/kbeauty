<?php

class Aitoc_Aitreports_Model_Export_Order extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();

        $this->_init('aitreports/export_order');
    }

    public function isOrdersExported($profile_id = 0)
    {
        $exportedOrders = $this->getCollection()
            ->loadByProfileId($profile_id);

        return sizeof($exportedOrders) == 1;
    } 
    
    public function assignOrders(Aitoc_Aitreports_Model_Export $export)
    {
        $this->getResource()->assignOrders($export);

        return $this;
    }
}
