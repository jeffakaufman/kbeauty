<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-08-12T15:09:07+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Observer/Cronjob.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Observer_Cronjob
{
    const CRON_CUSTOM = 'custom';
    const CRON_1MINUTE = '* * * * *';
    const CRON_5MINUTES = '*/5 * * * *';
    const CRON_10MINUTES = '*/10 * * * *';
    const CRON_15MINUTES = '*/15 * * * *';
    const CRON_20MINUTES = '*/20 * * * *';
    const CRON_HALFHOURLY = '*/30 * * * *';
    const CRON_HOURLY = '0 * * * *';
    const CRON_2HOURLY = '0 */2 * * *';
    const CRON_DAILY = '0 0 * * *';
    const CRON_TWICEDAILY = '0 0,12 * * *';

    public function import($schedule)
    {
        try {
            if (!Mage::helper('xtento_stockimport')->getModuleEnabled() || !Mage::helper('xtento_stockimport')->isModuleProperlyInstalled()) {
                return;
            }
            if (!$schedule) {
                return;
            }
            $jobCode = $schedule->getJobCode();
            preg_match('/xtento_stockimport_profile_(\d+)/', $jobCode, $jobMatch);
            if (!isset($jobMatch[1])) {
                Mage::throwException(Mage::helper('xtento_stockimport')->__('No profile ID found in job_code.'));
            }
            $profileId = $jobMatch[1];
            $profile = Mage::getModel('xtento_stockimport/profile')->load($profileId);
            if (!$profile->getId()) {
                Mage::throwException(Mage::helper('xtento_stockimport')->__('Profile ID %d does not seem to exist anymore.', $profileId));
            }
            if (!$profile->getEnabled()) {
                return; // Profile not enabled
            }
            if (!$profile->getCronjobEnabled()) {
                return; // Cronjob not enabled
            }
            $importModel = Mage::getModel('xtento_stockimport/import', array('profile' => $profile));
            $importModel->cronImport();
        } catch (Exception $e) {
            Mage::log('Cronjob exception for job_code ' . $jobCode . ': ' . $e->getMessage(), null, 'xtento_stockimport_cron.log', true);
            return;
        }
    }
}