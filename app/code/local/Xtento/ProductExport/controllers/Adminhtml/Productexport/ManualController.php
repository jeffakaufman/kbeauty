<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-11-03T20:58:37+01:00
 * File:          app/code/local/Xtento/ProductExport/controllers/Adminhtml/Productexport/ManualController.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Adminhtml_ProductExport_ManualController extends Xtento_ProductExport_Controller_Abstract
{
    /*
     * Export from grid handler
     */
    public function gridPostAction()
    {
        $exportType = $this->getRequest()->getParam('type', false);
        if (!$exportType) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Export type not specified.'));
            return $this->_redirectReferer();
        }
        $exportIds = $this->getRequest()->getPost($exportType, false);
        if (!$exportIds) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select objects to export.'));
            return $this->_redirectReferer();
        }
        $profileId = $this->getRequest()->getPost('profile_id', false);
        if (!$profileId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('No export profile specified.'));
            return $this->_redirectReferer();
        }
        $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
        // Export
        try {
            $beginTime = time();
            $exportedFiles = Mage::getModel('xtento_productexport/export', array('profile_id' => $profileId))->gridExport($exportIds);
            $endTime = time();
            if ($profile->getStartDownloadManualExport()) {
                return $this->_prepareFileDownload($exportedFiles);
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('Export of %d %s completed successfully in %d seconds. Click <a href="%s">here</a> to download exported files.', Mage::registry('product_export_log')->getRecordsExported(), Mage::helper('xtento_productexport/entity')->getPluralEntityName($profile->getEntity()), ($endTime - $beginTime), Mage::helper('adminhtml')->getUrl('*/productexport_log/download', array('id' => Mage::registry('product_export_log')->getId()))));
                if (Mage::registry('product_export_log')->getResult() !== Xtento_ProductExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__(nl2br(Mage::registry('product_export_log')->getResultMessage())));
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Error: %s', nl2br($e->getMessage())));
        }
        return $this->_redirectReferer();
    }

    /*
     * Manual export handler
     */
    public function manualPostAction()
    {
        $profileId = $this->getRequest()->getPost('profile_id');
        $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
        if (!$profile->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('No profile selected or this profile does not exist anymore.'));
            return $this->_redirectReferer();
        }
        // Prepare filters
        $filters = array();
        if ($this->getRequest()->getPost('store_id') !== NULL) {
            $storeIds = array();
            $postStoreIds = $this->getRequest()->getPost('store_id');
            if (isset($postStoreIds[0]) && strstr($postStoreIds[0], ',')) {
                $postStoreIds = explode(",", $postStoreIds[0]); // Comma multi select fix for file download JS
            }
            foreach ($postStoreIds as $storeId) {
                if ($storeId != '0' && $storeId != '') {
                    array_push($storeIds, $storeId);
                }
            }
            if (!empty($storeIds)) {
                $filters[] = array('store_id' => array('in' => $storeIds));
            }
        }
        if ($this->getRequest()->getPost('entity_from') !== NULL) {
            $filters[] = array('entity_id' => array('from' => $this->getRequest()->getPost('entity_from')));
        }
        if ($this->getRequest()->getPost('entity_to') !== NULL && $this->getRequest()->getPost('entity_to') !== '0') {
            $filters[] = array('entity_id' => array('to' => $this->getRequest()->getPost('entity_to')));
        }
        $dateRangeFilter = array();
        if ($this->getRequest()->getPost('daterange_from') != '') {
            $dateRangeFilter['date'] = true;
            $dateRangeFilter['from'] = Mage::helper('xtento_productexport/date')->convertDate($this->getRequest()->getPost('daterange_from'));
        }
        if ($this->getRequest()->getPost('daterange_to') != '') {
            $dateRangeFilter['date'] = true;
            $dateRangeFilter['to'] = Mage::helper('xtento_productexport/date')->convertDate($this->getRequest()->getPost('daterange_to') /*, false, true*/);
            $dateRangeFilter['to']->add('1', Zend_Date::DAY);
        }
        $profileFilterCreatedLastXDays = $profile->getData('export_filter_last_x_days');
        if (!empty($profileFilterCreatedLastXDays)) {
            $profileFilterCreatedLastXDays = preg_replace('/[^0-9]/', '', $profileFilterCreatedLastXDays);
            if ($profileFilterCreatedLastXDays >= 0) {
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
        #var_dump($filters); die();
        // Export
        try {
            $beginTime = time();
            $exportModel = Mage::getModel('xtento_productexport/export', array('profile' => $profile));
            if ($this->getRequest()->getPost('filter_new_only') == 'on') {
                $exportModel->setExportFilterNewOnly(true);
            }
            $exportedFiles = $exportModel->manualExport($filters);
            $endTime = time();
            $successMessage = Mage::helper('xtento_productexport')->__('Export of %d %s completed successfully in %d seconds. Click <a href="%s">here</a> to download exported files.', Mage::registry('product_export_log')->getRecordsExported(), Mage::helper('xtento_productexport/entity')->getPluralEntityName($profile->getEntity()), ($endTime - $beginTime), Mage::helper('adminhtml')->getUrl('*/productexport_log/download', array('id' => Mage::registry('product_export_log')->getId())));
            if ($this->getRequest()->getPost('start_download', false)) {
                Mage::getModel('core/cookie')->set('fileDownload', 'true', null, '/', '', null, false);
                Mage::getModel('core/cookie')->set('lastMessage', $successMessage, null, '/', '', null, false);
                if (Mage::registry('product_export_log')->getResult() !== Xtento_ProductExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getModel('core/cookie')->set('lastErrorMessage', Mage::helper('xtento_productexport')->__(nl2br(Mage::registry('product_export_log')->getResultMessage())), null, '/', '', null, false);
                } else {
                    Mage::getModel('core/cookie')->set('lastErrorMessage', '', null, '/', '', null, false);
                }
                return $this->_prepareFileDownload($exportedFiles);
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                if (Mage::registry('product_export_log')->getResult() !== Xtento_ProductExport_Model_Log::RESULT_SUCCESSFUL) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__(nl2br(Mage::registry('product_export_log')->getResultMessage())));
                }
                return $this->_redirect('*/productexport_manual/index', array('profile_id' => $profile->getId()));
            }
        } catch (Exception $e) {
            if ($this->getRequest()->getPost('start_download', false)) {
                Mage::getModel('core/cookie')->set('lastErrorMessage', Mage::helper('xtento_productexport')->__(nl2br($e->getMessage())), null, '/', '', null, false);
                Mage::getModel('core/cookie')->set('lastMessage', false, null, '/', '', null, false);
            } else {
                Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('%s', nl2br($e->getMessage())));
            }
            return $this->_redirect('*/productexport_manual/index', array('profile_id' => $profile->getId()));
        }
    }

    /*
     * Manual export
     */
    public function indexAction()
    {
        if (!Xtento_ProductExport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_productexport')->getModuleEnabled()) {
            return $this->_redirect('*/productexport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/productexport')
            ->_title(Mage::helper('xtento_productexport')->__('Product Export'))->_title(Mage::helper('xtento_productexport')->__('Manual Export'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/productexport/manual');
    }
}