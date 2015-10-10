<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-07-12T14:45:12+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Observer/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_ProductExport_Model_Observer_Abstract extends Mage_Core_Model_Abstract
{
    /*
     * Add store, date, status, ... filters based on profile settings
     */
    protected function _addProfileFilters($profile)
    {
        $filters = array();
        $dateRangeFilter = array();
        $profileFilterDatefrom = $profile->getExportFilterDatefrom();
        if (!empty($profileFilterDatefrom)) {
            $dateRangeFilter['date'] = true;
            $dateRangeFilter['from'] = Mage::helper('xtento_productexport/date')->convertDate($profileFilterDatefrom);
        }
        $profileFilterDateto = $profile->getExportFilterDateto();
        if (!empty($profileFilterDateto)) {
            $dateRangeFilter['date'] = true;
            $dateRangeFilter['to'] = Mage::helper('xtento_productexport/date')->convertDate($profileFilterDateto /*, false, true*/);
            $dateRangeFilter['to']->add('1', Zend_Date::DAY);
        }
        $profileFilterCreatedLastXDays = $profile->getData('export_filter_last_x_days');
        if (!empty($profileFilterCreatedLastXDays)) {
            $profileFilterCreatedLastXDays = intval(preg_replace('/[^0-9]/', '', $profileFilterCreatedLastXDays));
            if ($profileFilterCreatedLastXDays >= 0) {
                /*$dateToday = Mage::app()->getLocale()->date();
                $dateToday->sub($profileFilterCreatedLastXDays, Zend_Date::DAY);
                $dateRangeFilter['date'] = true;
                $dateRangeFilter['from'] = $dateToday->toString('yyyy-MM-dd 00:00:00');*/
                $dateToday = Zend_Date::now();
                $dateToday->sub($profileFilterCreatedLastXDays, Zend_Date::DAY);
                $dateToday->setHour(00);
                $dateToday->setSecond(00);
                $dateToday->setMinute(00);
                $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE));
                $dateRangeFilter['date'] = true;
                $dateRangeFilter['from'] = $dateToday->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }
        }
        if (!empty($dateRangeFilter)) {
            $filters[] = array('created_at' => $dateRangeFilter);
        }
        $profileFilterUpdatedLastXMinutes = $profile->getData('export_filter_updated_last_x_minutes');
        if (!empty($profileFilterUpdatedLastXMinutes)) {
            $profileFilterUpdatedLastXMinutes = preg_replace('/[^0-9]/', '', $profileFilterUpdatedLastXMinutes);
            if ($profileFilterUpdatedLastXMinutes >= 0) {
                $dateToday = Zend_Date::now();
                $dateToday->sub($profileFilterUpdatedLastXMinutes, Zend_Date::MINUTE);
                $dateToday->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                $dateToday->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::DEFAULT_TIMEZONE));
                $updatedAtFilter = array();
                $updatedAtFilter['date'] = true;
                $updatedAtFilter['from'] = $dateToday->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                $filters[] = array('updated_at' => $updatedAtFilter);
            }
        }
        return $filters;
    }
}