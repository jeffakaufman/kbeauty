<?php

class Aitoc_Aitreports_Model_Mysql4_Export_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitreports/export_order');
    }
    
    public function loadByProfileId($profile_id = 0)
    {
        $this->getSelect()
            ->where('profile_id = ?', $profile_id)
            ->limit(1);
        return $this;
    }

}
