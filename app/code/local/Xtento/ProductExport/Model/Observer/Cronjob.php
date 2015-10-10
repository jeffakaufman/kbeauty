<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-12-12T18:34:29+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Observer/Cronjob.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Observer_Cronjob extends Xtento_ProductExport_Model_Observer_Abstract
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

    public function export($schedule)
    {
        try {
            if (!Mage::helper('xtento_productexport')->getModuleEnabled() || !Mage::helper('xtento_productexport')->isModuleProperlyInstalled()) {
                return;
            }
            if (!$schedule) {
                return;
            }
            $jobCode = $schedule->getJobCode();
            preg_match('/xtento_productexport_profile_(\d+)/', $jobCode, $jobMatch);
            if (!isset($jobMatch[1])) {
                Mage::throwException(Mage::helper('xtento_productexport/export')->__('No profile ID found in job_code.'));
            }
            $profileId = $jobMatch[1];
            $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
            if (!$profile->getId()) {
                Mage::throwException(Mage::helper('xtento_productexport/export')->__('Profile ID %d does not seem to exist anymore.', $profileId));
            }
            if (!$profile->getEnabled()) {
                return; // Profile not enabled
            }
            if (!$profile->getCronjobEnabled()) {
                return; // Cronjob not enabled
            }
            $exportModel = Mage::getModel('xtento_productexport/export', array('profile' => $profile));
            $filters = $this->_addProfileFilters($profile);
            $exportModel->cronExport($filters);
        } catch (Exception $e) {
            Mage::log('Cronjob exception for job_code ' . $jobCode . ': ' . $e->getMessage(), null, 'xtento_productexport_cron.log', true);
            return;
        }
    }

    public function addProfileFilters($profile) {
        return $this->_addProfileFilters($profile);
    }
}