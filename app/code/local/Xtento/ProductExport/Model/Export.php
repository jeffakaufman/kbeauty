<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-02-24T22:07:15+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export extends Mage_Core_Model_Abstract
{
    /*
     * The actual export model handling object (product/category/...) exports
     */

    // Export entities
    const ENTITY_CATEGORY = 'category';
    const ENTITY_PRODUCT = 'product';

    // Export types
    const EXPORT_TYPE_TEST = 0; // Test Export
    const EXPORT_TYPE_GRID = 1; // Grid Export
    const EXPORT_TYPE_MANUAL = 2; // From "Manual Export" screen
    const EXPORT_TYPE_CRONJOB = 3; // Cronjob Export
    const EXPORT_TYPE_EVENT = 4; // Export after event

    public function _construct()
    {
        if ($this->getProfileId()) {
            $profile = Mage::getModel('xtento_productexport/profile')->load($this->getProfileId());
            $this->setProfile($profile);
        }
        parent::_construct();
    }

    public function getEntities()
    {
        $values = array();
        $values[Xtento_ProductExport_Model_Export::ENTITY_CATEGORY] = Mage::helper('xtento_productexport')->__('Categories');
        $values[Xtento_ProductExport_Model_Export::ENTITY_PRODUCT] = Mage::helper('xtento_productexport')->__('Products');
        return $values;
    }

    public function getExportTypes()
    {
        $values = array();
        $values[Xtento_ProductExport_Model_Export::EXPORT_TYPE_TEST] = Mage::helper('xtento_productexport')->__('Test Export');
        $values[Xtento_ProductExport_Model_Export::EXPORT_TYPE_MANUAL] = Mage::helper('xtento_productexport')->__('Manual Export');
        $values[Xtento_ProductExport_Model_Export::EXPORT_TYPE_GRID] = Mage::helper('xtento_productexport')->__('Grid Export');
        $values[Xtento_ProductExport_Model_Export::EXPORT_TYPE_CRONJOB] = Mage::helper('xtento_productexport')->__('Cronjob Export');
        $values[Xtento_ProductExport_Model_Export::EXPORT_TYPE_EVENT] = Mage::helper('xtento_productexport')->__('Event Export');
        return $values;
    }

    /*
     * Validate XSL Template function used to run a test export when editing a profile
     */
    public function testExport($exportId = false)
    {
        if (empty($exportId)) {
            return Mage::helper('xtento_productexport')->__('No test ID to export specified.');
        }
        $this->setExportType(self::EXPORT_TYPE_TEST);
        $filters[] = array('entity_id' => array('in' => explode(",", $exportId)));
        $exportedFiles = $this->_runExport($filters);
        return $exportedFiles;
    }

    public function gridExport($exportIds)
    {
        if (empty($exportIds)) {
            Mage::throwException(Mage::helper('xtento_productexport')->__('No %s to export specified.', Mage::helper('xtento_productexport/entity')->getPluralEntityName($this->getProfile()->getEntity())));
        }
        $this->_checkStatus();
        $this->setExportType(self::EXPORT_TYPE_GRID);
        $this->_beforeExport();
        $filters[] = array('entity_id' => array('in' => $exportIds));
        $generatedFiles = $this->_runExport($filters);
        if ($this->getProfile()->getSaveFilesManualExport()) {
            $this->_saveFiles();
        }
        $this->_afterExport();
        return $generatedFiles;
    }

    public function manualExport($filters)
    {
        $this->_checkStatus();
        $this->setExportType(self::EXPORT_TYPE_MANUAL);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters);
        if ($this->getProfile()->getSaveFilesManualExport()) {
            $this->_saveFiles();
        }
        $this->_afterExport();
        return $generatedFiles;
    }

    public function eventExport($filters)
    {
        $this->setExportType(self::EXPORT_TYPE_EVENT);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters);
        if (empty($generatedFiles)) {
            $this->getLogEntry()->delete();
            return false;
        }
        $this->_saveFiles();
        $this->_afterExport();
        return true;
    }

    public function cronExport($filters)
    {
        $this->setExportType(self::EXPORT_TYPE_CRONJOB);
        $this->_beforeExport();
        $generatedFiles = $this->_runExport($filters);
        if (empty($generatedFiles)) {
            $this->getLogEntry()->delete();
            return false;
        }
        $this->_saveFiles();
        $this->_afterExport();
        return true;
    }

    private function _runExport($filters)
    {
        try {
            @set_time_limit(0);
            Mage::helper('xtcore/utils')->increaseMemoryLimit('1024M');
            if (!$this->getProfile()) {
                Mage::throwException(Mage::helper('xtento_productexport')->__('No profile to export specified.'));
            }
            $returnArray = $this->_exportObjects($filters);
            if (empty($returnArray)) {
                Mage::throwException(Mage::helper('xtento_productexport')->__('0 %s have been exported.', Mage::helper('xtento_productexport/entity')->getPluralEntityName($this->getProfile()->getEntity())));
            }
            $this->setReturnArrayWithObjects($returnArray);
            // Get output type
            if ($this->getProfile()->getOutputType() == 'csv') {
                $type = 'csv';
            } else if ($this->getProfile()->getOutputType() == 'xml') {
                $type = 'xml';
            } else {
                $type = 'xsl';
            }
            // Convert data
            if ($this->getProfile()->getExportOneFilePerObject()) {
                // Create one file per exported object - !! ATTENTION !! - make sure file names are different.
                $generatedFiles = array();
                foreach ($this->getReturnArrayWithObjects() as $returnObject) {
                    $generatedFiles = array_merge(
                        $generatedFiles,
                        Mage::getModel('xtento_productexport/output_' . $type, array('profile' => $this->getProfile()))->convertData(array($returnObject))
                    );
                }
            } else {
                // Create just one file for all exported objects
                $generatedFiles = Mage::getModel('xtento_productexport/output_' . $type, array('profile' => $this->getProfile()))->convertData($this->getReturnArrayWithObjects());
            }
            $this->setGeneratedFiles($generatedFiles);
            if (is_array($this->getReturnArrayWithObjects()) && $this->getLogEntry()) {
                $this->getLogEntry()->setRecordsExported(count($this->getReturnArrayWithObjects()));
            }
            return $generatedFiles;
        } catch (Exception $e) {
            if ($this->getLogEntry()) {
                $result = Xtento_ProductExport_Model_Log::RESULT_FAILED;
                if (preg_match('/have been exported/', $e->getMessage())) {
                    if ($this->getExportType() == self::EXPORT_TYPE_MANUAL || $this->getExportType() == self::EXPORT_TYPE_GRID) {
                        $result = Xtento_ProductExport_Model_Log::RESULT_WARNING;
                    } else {
                        return array();
                    }
                }
                $this->getLogEntry()->setResult($result);
                $this->getLogEntry()->addResultMessage($e->getMessage());
                $this->_afterExport();
            }
            if ($this->getExportType() == self::EXPORT_TYPE_MANUAL || $this->getExportType() == self::EXPORT_TYPE_GRID || $this->getExportType() == self::EXPORT_TYPE_TEST) {
                Mage::throwException($e->getMessage());
            }
            return array();
        }
    }

    private function _exportObjects($filters)
    {
        $export = Mage::getModel('xtento_productexport/export_entity_' . $this->getProfile()->getEntity());
        $export->setExportType($this->getExportType());
        $collection = $export->setCollectionFilters($filters);
        if ($this->getProfile()->getExportFilterNewOnly() && ($this->getExportType() == self::EXPORT_TYPE_CRONJOB || $this->getExportType() == self::EXPORT_TYPE_EVENT)) {
            $this->_addExportOnlyNewFilter($collection);
        }
        if ($this->getExportFilterNewOnly() && ($this->getExportType() == self::EXPORT_TYPE_MANUAL /* || $this->getExportType() == self::EXPORT_TYPE_GRID*/)) {
            $this->_addExportOnlyNewFilter($collection);
        }
        $export->setProfile($this->getProfile());
        return $export->runExport();
    }

    private function _addExportOnlyNewFilter($collection)
    {
        // Filter and hide objects that have been exported previously
        $collection->getSelect()->joinLeft(
            array('export_history' => $collection->getTable('xtento_productexport/history')),
            'e.entity_id = export_history.entity_id and ' . $collection->getConnection()->quoteInto('export_history.entity = ?', $this->getProfile()->getEntity()) . ' and ' . $collection->getConnection()->quoteInto('export_history.profile_id = ?', $this->getProfile()->getId()),
            array()
        );
        $collection->getSelect()->where('export_history.entity_id IS NULL');
        #echo $collection->getSelect(); die();
    }

    /*
     * Save files on their destinations
     */
    private function _saveFiles()
    {
        try {
            foreach ($this->getProfile()->getDestinations() as $destination) {
                try {
                    $savedFiles = $destination->saveFiles($this->getGeneratedFiles());
                    if (is_array($this->getFiles()) && is_array($savedFiles)) {
                        $this->setFiles(array_merge($this->getFiles(), $savedFiles));
                    } else {
                        $this->setFiles($savedFiles);
                    }
                } catch (Exception $e) {
                    $this->getLogEntry()->setResult(Xtento_ProductExport_Model_Log::RESULT_WARNING);
                    $this->getLogEntry()->addResultMessage($e->getMessage());
                }
            }
        } catch (Exception $e) {
            $this->getLogEntry()->setResult(Xtento_ProductExport_Model_Log::RESULT_FAILED);
            $this->getLogEntry()->addResultMessage($e->getMessage());
            if ($this->getExportType() == self::EXPORT_TYPE_MANUAL) {
                Mage::throwException($e->getMessage());
            }
        }
    }

    private function _beforeExport()
    {
        $this->setBeginTime(time());
        #$memBefore = memory_get_usage();
        #$timeBefore = time();
        #echo "Before export: " . $memBefore . " bytes / Time: " . $timeBefore . "<br>";
        $logEntry = Mage::getModel('xtento_productexport/log');
        $logEntry->setCreatedAt(now());
        $logEntry->setProfileId($this->getProfile()->getId());
        $logEntry->setDestinationIds($this->getProfile()->getDestinationIds());
        $logEntry->setExportType($this->getExportType());
        $logEntry->setRecordsExported(0);
        $logEntry->setResultMessage(Mage::helper('xtento_productexport')->__('Export started...'));
        $logEntry->save();
        $this->setLogEntry($logEntry);
        Mage::unregister('product_export_log');
        Mage::unregister('product_export_profile');
        Mage::register('product_export_log', $logEntry);
        Mage::register('product_export_profile', $this->getProfile());
    }

    private function _afterExport()
    {
        if ($this->getLogEntry()->getResult() !== Xtento_ProductExport_Model_Log::RESULT_FAILED) {
            if ($this->getProfile()->getExportFilterNewOnly() || $this->getExportFilterNewOnly()) {
                $this->_createExportHistoryEntries();
            }
        }
        $this->_saveLog();
        Mage::unregister('product_export_profile');
        #echo "After export: " . memory_get_usage() . " (Difference: " . round((memory_get_usage() - $memBefore) / 1024 / 1024, 2) . " MB, " . (time() - $timeBefore) . " Secs) - Count: " . (count($exportIds)) . " -  Per entry: " . round(((memory_get_usage() - $memBefore) / 1024 / 1024) / (count($exportIds)), 2) . "<br>";
    }

    private function _createExportHistoryEntries()
    {
        if ($this->getReturnArrayWithObjects()) {
            // Save exported object ids in the export history
            foreach ($this->getReturnArrayWithObjects() as $object) {
                $historyEntry = Mage::getModel('xtento_productexport/history');
                $historyEntry->setProfileId($this->getProfile()->getId());
                $historyEntry->setLogId($this->getLogEntry()->getId());
                $historyEntry->setEntity($this->getProfile()->getEntity());
                $historyEntry->setEntityId($object['entity_id']);
                $historyEntry->setExportedAt(now());
                $historyEntry->save();
            }
        }
    }

    private function _saveLog()
    {
        $this->_saveLastExecutionNow();
        if (is_array($this->getFiles())) {
            $this->getLogEntry()->setFiles(implode("|", $this->getFiles()));
        }
        $this->getLogEntry()->setResult($this->getLogEntry()->getResult() ? $this->getLogEntry()->getResult() : Xtento_ProductExport_Model_Log::RESULT_SUCCESSFUL);
        $this->getLogEntry()->setResultMessage($this->getLogEntry()->getResultMessages() ? $this->getLogEntry()->getResultMessages() : Mage::helper('xtento_productexport')->__('Export of %d %s finished in %d seconds.', $this->getLogEntry()->getRecordsExported(), Mage::helper('xtento_productexport/entity')->getPluralEntityName($this->getProfile()->getEntity()), (time() - $this->getBeginTime())));
        $this->getLogEntry()->save();
        $this->_errorEmailNotification();
        #Mage::unregister('product_export_log');
    }

    private function _saveLastExecutionNow()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->update(
            $this->getProfile()->_getResource()->getMainTable(),
            array('last_execution' => now()),
            array("`{$this->getProfile()->_getResource()->getIdFieldName()}` = {$this->getProfile()->getId()}")
        );
    }

    private function _errorEmailNotification()
    {
        if (!Mage::helper('xtento_productexport')->isDebugEnabled() || Mage::helper('xtento_productexport')->getDebugEmail() == '') {
            return $this;
        }
        if ($this->getLogEntry()->getResult() >= Xtento_ProductExport_Model_Log::RESULT_WARNING) {
            try {
                $mail = new Zend_Mail();
                $mail->setFrom('store@' . @$_SERVER['SERVER_NAME'], @$_SERVER['SERVER_NAME']);
                foreach (explode(",", Mage::helper('xtento_productexport')->getDebugEmail()) as $emailAddress) {
                    $emailAddress = trim($emailAddress);
                    $mail->addTo($emailAddress, $emailAddress);
                }
                $mail->setSubject('Magento Product Export Module @ ' . @$_SERVER['SERVER_NAME']);
                $mail->setBodyText('Warning/Error/Message(s): ' . $this->getLogEntry()->getResultMessages());
                $mail->send(Mage::helper('xtcore/utils')->getEmailTransport());
            } catch (Exception $e) {
                $this->getLogEntry()->addResultMessage('Exception: ' . $e->getMessage());
                $this->getLogEntry()->setResult(Xtento_ProductExport_Model_Log::RESULT_WARNING);
                $this->getLogEntry()->setResultMessage($this->getLogEntry()->getResultMessages());
                $this->getLogEntry()->save();
            }
        }
        return $this;
    }

    private function _checkStatus()
    {
        if (!Xtento_ProductExport_Model_System_Config_Source_Order_Status::isEnabled()) {
            Mage::throwException(Mage::helper('xtento_productexport')->getMsg());
        }
    }

    private function _getExperimentalFeatureSupport()
    {
        $experimentalFeatureDataFile = Mage::helper('xtcore/filesystem')->getModuleDir($this) . DS . 'xtento' . DS . 'experimental_features.xml';
        if (@file_exists($experimentalFeatureDataFile)) {
            return true;
        }
        return false;
    }
}