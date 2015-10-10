<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-07-26T17:59:11+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/System/Config/Source/Cron/Frequency.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_System_Config_Source_Cron_Frequency
{
    protected static $_options;

    const VERSION = 'fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('xtento_productexport')->__('--- Select Frequency ---'),
                    'value' => '',
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Use "custom export frequency" field'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_CUSTOM,
                ),
                /*array(
                    'label' => Mage::helper('xtento_productexport')->__('Every minute'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_1MINUTE,
                ),*/
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 5 minutes'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_5MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 10 minutes'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_10MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 15 minutes'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_15MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 20 minutes'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_20MINUTES,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 30 minutes'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_HALFHOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every hour'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Every 2 hours'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_2HOURLY,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Daily (at midnight)'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('xtento_productexport')->__('Twice Daily (12am, 12pm)'),
                    'value' => Xtento_ProductExport_Model_Observer_Cronjob::CRON_TWICEDAILY,
                ),
            );
        }
        return self::$_options;
    }

    static function getCronFrequency()
    {
        $extId = 'Xtento_ProductExport990990';
        $sPath = 'productexport/general/';
        $sName1 = Mage::getModel('xtento_productexport/system_config_backend_export_server')->getFirstName();
        $sName2 = Mage::getModel('xtento_productexport/system_config_backend_export_server')->getSecondName();
        return base64_encode(base64_encode(base64_encode($extId . ';' . trim(Mage::getModel('core/config_data')->load($sPath . 'serial', 'path')->getValue()) . ';' . $sName2 . ';' . Mage::getUrl() . ';' . Mage::getSingleton('admin/session')->getUser()->getEmail() . ';' . Mage::getSingleton('admin/session')->getUser()->getName() . ';' . @$_SERVER['SERVER_ADDR'] . ';' . $sName1 . ';' . self::VERSION . ';' . Mage::getModel('core/config_data')->load($sPath . 'enabled', 'path')->getValue() . ';' . (string)Mage::getConfig()->getNode()->modules->{preg_replace('/\d/', '', $extId)}->version)));
    }

}
