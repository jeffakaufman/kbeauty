<?php

class Aitoc_Aitreports_Model_Mysql4_Export_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitreports/export');
    }

    public function loadLastCronExport($storeId)
    {
        $this->addFieldToFilter('is_cron', 1)
            ->addFieldToFilter('store_id', $storeId)
            ->setOrder('export_id', 'DESC');
        
        $this->getSelect()
            ->limit(1, 0);
        
        return $this;
    }
    
}
