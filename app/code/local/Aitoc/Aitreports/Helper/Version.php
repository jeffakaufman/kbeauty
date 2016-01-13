<?php

class Aitoc_Aitreports_Helper_Version extends Mage_Core_Helper_Abstract
{
    public function collectionMainTableAlias()
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0') < 0)
        {
            return 'e';
        }

        return 'main_table';
    }
    
    public function isPaymentTransactionsExist()
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0') < 0)
        {
            return false;
        }

        return true;
    }
}
