<?php
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
set_time_limit(0);
ini_set('memory_limit','1024M');

function _addProfileFilters_agent($profile)
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

try {
    // if (!Mage::helper('xtento_productexport')->getModuleEnabled() || !Mage::helper('xtento_productexport')->isModuleProperlyInstalled()) {
    //     return;
    // }
    // if (!$schedule) {
    //     return;
    // }
    // $jobCode = $schedule->getJobCode();
    // preg_match('/xtento_productexport_profile_(\d+)/', $jobCode, $jobMatch);
    // if (!isset($jobMatch[1])) {
    //     Mage::throwException(Mage::helper('xtento_productexport/export')->__('No profile ID found in job_code.'));
    // }
    $profileId = 2;
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
    $filters = _addProfileFilters_agent($profile);
    $exportModel->cronExport($filters);
} catch (Exception $e) {
    Mage::log('Cronjob exception for job_code ' . $jobCode . ': ' . $e->getMessage(), null, 'xtento_productexport_cron.log', true);
    return;
}



?>