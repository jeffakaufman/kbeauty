<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-07-26T18:00:19+02:00
 * File:          app/code/local/Xtento/StockImport/Model/System/Config/Source/Cron/Frequency.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_System_Config_Source_Cron_Frequency
{
    protected static $_options;

    const VERSION = 'WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('--- Select Frequency ---'),
                    'value' => '',
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Use "custom import frequency" field'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_CUSTOM,
                ),
                /*array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every minute'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_1MINUTE,
                ),*/
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 5 minutes'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_5MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 10 minutes'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_10MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 15 minutes'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_15MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 20 minutes'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_20MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 30 minutes'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_HALFHOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every hour'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Every 2 hours'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_2HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Daily (at midnight)'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('xtento_stockimport')->__('Twice Daily (12am, 12pm)'),
                    'value' => Xtento_StockImport_Model_Observer_Cronjob::CRON_TWICEDAILY,
                ),
            );
        }
        return self::$_options;
    }

    static function getCronFrequency()
    {
        $extId = 'Xtento_InventoryImport996581';
        $sPath = 'stockimport/general/';
        $sName1 = Mage::getModel('xtento_stockimport/system_config_backend_import_server')->getFirstName();
        $sName2 = Mage::getModel('xtento_stockimport/system_config_backend_import_server')->getSecondName();
        return base64_encode(base64_encode(base64_encode($extId . ';' . trim(Mage::getModel('core/config_data')->load($sPath . 'serial', 'path')->getValue()) . ';' . $sName2 . ';' . Mage::getUrl() . ';' . Mage::getSingleton('admin/session')->getUser()->getEmail() . ';' . Mage::getSingleton('admin/session')->getUser()->getName() . ';' . @$_SERVER['SERVER_ADDR'] . ';' . $sName1 . ';' . self::VERSION . ';' . Mage::getModel('core/config_data')->load($sPath . 'enabled', 'path')->getValue() . ';' . (string)Mage::getConfig()->getNode()->modules->{preg_replace(array('/\d/', '/Inventory/'), array('', 'Stock'), $extId)}->version)));
    }

}
