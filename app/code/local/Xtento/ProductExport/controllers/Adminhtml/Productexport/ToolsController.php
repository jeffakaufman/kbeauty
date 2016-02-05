<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-04-11T21:31:51+02:00
 * File:          app/code/local/Xtento/ProductExport/controllers/Adminhtml/Productexport/ToolsController.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Adminhtml_Productexport_ToolsController extends Xtento_ProductExport_Controller_Abstract
{
    /*
     * Misc. Tools
     */
    public function indexAction()
    {
        if (!Xtento_ProductExport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_productexport')->getModuleEnabled()) {
            return $this->_redirect('*/productexport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    public function exportSettingsAction()
    {
        $profileIds = $this->getRequest()->getPost('profile_ids', array());
        $destinationIds = $this->getRequest()->getPost('destination_ids', array());
        if (empty($profileIds) && empty($destinationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('No profiles / destinations to export specified.'));
            return $this->_redirectReferer();
        }
        $randIdPrefix = rand(10000, 99999);
        $exportData = array();
        $exportData['profiles'] = array();
        $exportData['destinations'] = array();
        foreach ($profileIds as $profileId) {
            $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
            $profile->unsetData('profile_id');
            $profileDestinationIds = $profile->getData('destination_ids');
            $newDestinationIds = array();
            foreach (explode("&", $profileDestinationIds) as $destinationId) {
                if (is_numeric($destinationId)) {
                    $newDestinationIds[] = $randIdPrefix . $destinationId;
                }
            }
            $profile->setData('new_destination_ids', implode("&", $newDestinationIds));
            $exportData['profiles'][] = $profile->toArray();
        }
        foreach ($destinationIds as $destinationId) {
            $destination = Mage::getModel('xtento_productexport/destination')->load($destinationId);
            $destination->setData('new_destination_id', $randIdPrefix . $destination->getDestinationId());
            #$destination->unsetData('destination_id');
            $destination->unsetData('password');
            $exportData['destinations'][] = $destination->toArray();
        }
        $exportData = Zend_Json::encode($exportData);
        return $this->_prepareFileDownload(array('xtento_productexport_settings.json' => $exportData));
    }

    public function importSettingsAction()
    {
        // Check for uploaded file
        $uploadedFile = @$_FILES['settings_file']['tmp_name'];
        if (empty($uploadedFile)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('No settings file has been uploaded.'));
            return $this->_redirectReferer();
        }
        // Check if data should be updated or added
        $updateByName = $this->getRequest()->getPost('update_by_name', false);
        if ($updateByName == 'on') {
            $updateByName = true;
        } else {
            $updateByName = false;
        }
        // Counters
        $addedCounter = array('profiles' => 0, 'destinations' => 0);
        $updatedCounter = array('profiles' => 0, 'destinations' => 0);
        // Load and decode JSON settings
        $settingsFile = file_get_contents($uploadedFile);
        try {
            $settingsArray = @Zend_Json::decode($settingsFile);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Import failed. Decoding of JSON import format failed.'));
            return $this->_redirectReferer();
        }
        // Process profiles
        if (isset($settingsArray['profiles'])) {
            foreach ($settingsArray['profiles'] as $profileData) {
                if ($updateByName) {
                    $profileCollection = Mage::getModel('xtento_productexport/profile')->getCollection()
                        ->addFieldToFilter('entity', $profileData['entity'])
                        ->addFieldToFilter('name', $profileData['name']);
                    if ($profileCollection->count() === 1) {
                        unset($profileData['new_destination_ids']);
                        Mage::getModel('xtento_productexport/profile')->setData($profileData)->setId($profileCollection->getFirstItem()->getId())->save();
                        $updatedCounter['profiles']++;
                    } else {
                                                if (isset($profileData['new_destination_ids'])) {
                            $profileData['destination_ids'] = $profileData['new_destination_ids'];
                            unset($profileData['new_destination_ids']);
                        }
                        Mage::getModel('xtento_productexport/profile')->setData($profileData)->save();
                        $addedCounter['profiles']++;
                    }
                } else {
                    if (isset($profileData['new_destination_ids'])) {
                        $profileData['destination_ids'] = $profileData['new_destination_ids'];
                        unset($profileData['new_destination_ids']);
                    }
                    Mage::getModel('xtento_productexport/profile')->setData($profileData)->save();
                    $addedCounter['profiles']++;
                }
            }
        }
        // Process destinations
        if (isset($settingsArray['destinations'])) {
            foreach ($settingsArray['destinations'] as $destinationData) {
                if ($updateByName) {
                    $destinationCollection = Mage::getModel('xtento_productexport/destination')->getCollection()
                        ->addFieldToFilter('type', $destinationData['type'])
                        ->addFieldToFilter('name', $destinationData['name']);
                    if ($destinationCollection->count() === 1) {
                        unset($destinationData['new_destination_id']);
                        Mage::getModel('xtento_productexport/destination')->setData($destinationData)->setId($destinationCollection->getFirstItem()->getId())->save();
                        $updatedCounter['destinations']++;
                    } else {
                        $newDestination = Mage::getModel('xtento_productexport/destination')->setData($destinationData);
                        if (isset($destinationData['new_destination_id'])) {
                            $newDestination->setId($destinationData['new_destination_id']);
                            unset($destinationData['new_destination_id']);
                            $newDestination->saveWithId();
                        } else {
                            unset($destinationData['new_destination_id']);
                            $newDestination->save();
                        }
                        $addedCounter['destinations']++;
                    }
                } else {
                    $newDestination = Mage::getModel('xtento_productexport/destination')->setData($destinationData);
                    if (isset($destinationData['new_destination_id'])) {
                        $newDestination->setId($destinationData['new_destination_id']);
                        unset($destinationData['new_destination_id']);
                        $newDestination->saveWithId();
                    } else {
                        unset($destinationData['new_destination_id']);
                        $newDestination->save();
                    }
                    $addedCounter['destinations']++;
                }
            }
        }
        // Done
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('%d profiles have been added, %d profiles have been updated, %d destinations have been added, %d destinations have been updated.', $addedCounter['profiles'], $updatedCounter['profiles'], $addedCounter['destinations'], $updatedCounter['destinations']));
        return $this->_redirectReferer();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/productexport')
            ->_title(Mage::helper('xtento_productexport')->__('Product Export'))->_title(Mage::helper('xtento_productexport')->__('Tools'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/productexport/tools');
    }

}