<?php

/**
 * Product:       Xtento_TrackingImport (2.1.2)
 * ID:            9Hiec+sXo9Z8XvyLsrsgPILEHN0W5+Sn/0xZtemTYL0=
 * Packaged:      2015-11-13T21:34:24+00:00
 * Last Modified: 2013-11-09T12:56:06+01:00
 * File:          app/code/local/Xtento/TrackingImport/Model/Source/Custom.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Model_Source_Custom extends Xtento_TrackingImport_Model_Source_Abstract
{
    public function testConnection()
    {
        $this->initConnection();
        return $this->getTestResult();
    }

    public function initConnection()
    {
        $this->setSource(Mage::getModel('xtento_trackingimport/source')->load($this->getSource()->getId()));
        $testResult = new Varien_Object();
        $this->setTestResult($testResult);
        if (!@Mage::getModel($this->getSource()->getCustomClass())) {
            $this->getTestResult()->setSuccess(false)->setMessage(Mage::helper('xtento_trackingimport')->__('Custom class NOT found.'));
            $this->getSource()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage($this->getTestResult()->getMessage())->save();
            return false;
        } else {
            $this->getTestResult()->setSuccess(true)->setMessage(Mage::helper('xtento_trackingimport')->__('Custom class found and ready to use.'));
            $this->getSource()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage($this->getTestResult()->getMessage())->save();
            return true;
        }
    }

    public function loadFiles()
    {
        // Init connection
        if (!$this->initConnection()) {
            return false;
        }
        // Call custom class
        $filesToProcess = @Mage::getModel($this->getSource()->getCustomClass())->loadFiles();
        return $filesToProcess;
    }

    public function archiveFiles($filesToProcess, $forceDelete = false)
    {
        // Init connection
        if (!$this->initConnection()) {
            return false;
        }
        $logEntry = Mage::registry('tracking_import_log');
        @Mage::getModel($this->getSource()->getCustomClass())->archiveFiles();
    }
}