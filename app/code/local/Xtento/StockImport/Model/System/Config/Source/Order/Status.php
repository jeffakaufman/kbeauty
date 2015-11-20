<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-07-26T18:00:34+02:00
 * File:          app/code/local/Xtento/StockImport/Model/System/Config/Source/Order/Status.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $statuses[] = array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- No change --'));

        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            # Support for Custom Order Status introduced in Magento version 1.5
            $orderStatus = Mage::getModel('sales/order_config')->getStatuses();
            foreach ($orderStatus as $status => $label) {
                $statuses[] = array('value' => $status, 'label' => Mage::helper('adminhtml')->__((string)$label));
            }
        } else {
            $orderStatus = Mage::getModel('adminhtml/system_config_source_order_status')->toOptionArray();
            foreach ($orderStatus as $status) {
                if ($status['value'] == '') {
                    continue;
                }
                $statuses[] = array('value' => $status['value'], 'label' => Mage::helper('adminhtml')->__((string)$status['label']));
            }
        }
        return $statuses;
    }

    // Function to just put all order status "codes" into an array.
    public function toArray()
    {
        $statuses = $this->toOptionArray();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['value']];
        }
        return $statusArray;
    }

    static function isEnabled()
    {
        $extId = 'Xtento_InventoryImport996581';
        $sPath = 'stockimport/general/';
        $sName = Mage::getModel('xtento_stockimport/system_config_backend_import_server')->getFirstName();
        $sName2 = Mage::getModel('xtento_stockimport/system_config_backend_import_server')->getSecondName();
        $s = trim(Mage::getModel('core/config_data')->load($sPath . 'serial', 'path')->getValue());
        if (($s !== sha1(sha1($extId . '_' . $sName))) && $s !== sha1(sha1($extId . '_' . $sName2))) {
            Mage::getConfig()->saveConfig($sPath . 'enabled', 0);
            Mage::getConfig()->cleanCache();
            return false;
        } else {
            return true;
        }
    }
}
