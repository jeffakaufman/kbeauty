<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-11-21T15:43:06+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Profile.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Profile extends Mage_Rule_Model_Rule
{
    /*
     * Profile model containing information about export profiles
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('xtento_productexport/profile');
    }

    public function getConditionsInstance()
    {
        Mage::register('product_export_profile', $this, true);
        return Mage::getModel('xtento_productexport/export_condition_combine');
    }

    public function getDestinations()
    {
        $logEntry = Mage::registry('product_export_log');
        $destinationIds = array_filter(explode("&", $this->getData('destination_ids')));
        $destinations = array();
        foreach ($destinationIds as $destinationId) {
            if (!is_numeric($destinationId)) {
                continue;
            }
            $destination = Mage::getModel('xtento_productexport/destination')->load($destinationId);
            if ($destination->getId()) {
                $destinations[] = $destination;
            } else {
                #if ($logEntry) {
                    #$logEntry->setResult(Xtento_ProductExport_Model_Log::RESULT_WARNING);
                    #$logEntry->addResultMessage(Mage::helper('xtento_productexport')->__('Destination ID "%s" could not be found.', $destinationId));
                #}
            }
        }
        if ($this->getSaveFilesLocalCopy()) {
            // Add "faked" local destination to save copies of all exports in ./var/export_bkp/
            $destination = Mage::getModel('xtento_productexport/destination');
            $destination->setBackupDestination(true);
            $destination->setName("Backup Local Destination");
            $destination->setType(Xtento_ProductExport_Model_Destination::TYPE_LOCAL);
            $destination->setPath(Mage::helper('xtento_productexport/export')->getExportBkpDir());
            $destinations[] = $destination;
        }
        // Return destinations
        return $destinations;
    }

    protected function _beforeSave()
    {
        // Only call the "rule" model parents _beforeSave function if the profile is modified in the backend, as otherwise the "conditions" ("export filters") could be lost
        if (Mage::app()->getRequest()->getControllerName() == 'productexport_profile') {
            parent::_beforeSave();
        } else {
            if (!$this->getId()) {
                $this->isObjectNew(true);
            }
        }
    }
}