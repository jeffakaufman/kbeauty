<?php

class Aitoc_Aitreports_Model_Mysql4_Profile extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('aitreports/profile', 'profile_id');
    }
}
