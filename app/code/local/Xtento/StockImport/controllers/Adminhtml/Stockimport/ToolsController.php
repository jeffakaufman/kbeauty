<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-07-03T00:03:35+02:00
 * File:          app/code/local/Xtento/StockImport/controllers/Adminhtml/Stockimport/ToolsController.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Adminhtml_StockImport_ToolsController extends Xtento_StockImport_Controller_Abstract
{
    /*
     * Misc. Tools
     */
    public function indexAction()
    {
        if (!Xtento_StockImport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_stockimport')->getModuleEnabled()) {
            return $this->_redirect('*/stockimport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    public function exportSettingsAction()
    {
        $profileIds = $this->getRequest()->getPost('profile_ids', array());
        $sourceIds = $this->getRequest()->getPost('source_ids', array());
        if (empty($profileIds) && empty($sourceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('No profiles / sources to export specified.'));
            return $this->_redirectReferer();
        }
        $randIdPrefix = rand(10000, 99999);
        $importData = array();
        $importData['profiles'] = array();
        $importData['sources'] = array();
        foreach ($profileIds as $profileId) {
            $profile = Mage::getModel('xtento_stockimport/profile')->load($profileId);
            $profile->unsetData('profile_id');
            $profileSourceIds = $profile->getData('source_ids');
            $newSourceIds = array();
            foreach (explode("&", $profileSourceIds) as $sourceId) {
                if (is_numeric($sourceId)) {
                    $newSourceIds[] = $randIdPrefix . $sourceId;
                }
            }
            $profile->setData('new_source_ids', implode("&", $newSourceIds));
            $importData['profiles'][] = $profile->toArray();
        }
        foreach ($sourceIds as $sourceId) {
            $source = Mage::getModel('xtento_stockimport/source')->load($sourceId);
            $source->setData('new_source_id', $randIdPrefix . $source->getSourceId());
            #$source->unsetData('source_id');
            $source->unsetData('password');
            $importData['sources'][] = $source->toArray();
        }
        $importData = Zend_Json::encode($importData);
        return $this->_prepareFileDownload(array('xtento_stockimport_settings.json' => $importData));
    }

    public function importSettingsAction()
    {
        // Check for uploaded file
        $uploadedFile = @$_FILES['settings_file']['tmp_name'];
        if (empty($uploadedFile)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('No settings file has been uploaded.'));
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
        $addedCounter = array('profiles' => 0, 'sources' => 0);
        $updatedCounter = array('profiles' => 0, 'sources' => 0);
        // Load and decode JSON settings
        $settingsFile = file_get_contents($uploadedFile);
        try {
            $settingsArray = @Zend_Json::decode($settingsFile);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('Import failed. Decoding of JSON import format failed.'));
            return $this->_redirectReferer();
        }
        // Process profiles
        if (isset($settingsArray['profiles'])) {
            foreach ($settingsArray['profiles'] as $profileData) {

                if ($updateByName) {
                    $profileCollection = Mage::getModel('xtento_stockimport/profile')->getCollection()
                        ->addFieldToFilter('entity', $profileData['entity'])
                        ->addFieldToFilter('name', $profileData['name']);
                    if ($profileCollection->count() === 1) {
                        unset($profileData['new_source_ids']);
                        Mage::getModel('xtento_stockimport/profile')->setData($profileData)->setId($profileCollection->getFirstItem()->getId())->save();
                        $updatedCounter['profiles']++;
                    } else {
                        if (isset($profileData['new_source_ids'])) {
                            $profileData['source_ids'] = $profileData['new_source_ids'];
                            unset($profileData['new_source_ids']);
                        }
                        Mage::getModel('xtento_stockimport/profile')->setData($profileData)->save();
                        $addedCounter['profiles']++;
                    }
                } else {
                    if (isset($profileData['new_source_ids'])) {
                        $profileData['source_ids'] = $profileData['new_source_ids'];
                        unset($profileData['new_source_ids']);
                    }
                    Mage::getModel('xtento_stockimport/profile')->setData($profileData)->save();
                    $addedCounter['profiles']++;
                }
            }
        }
        // Process sources
        if (isset($settingsArray['sources'])) {
            foreach ($settingsArray['sources'] as $sourceData) {
                if ($updateByName) {
                    $sourceCollection = Mage::getModel('xtento_stockimport/source')->getCollection()
                        ->addFieldToFilter('type', $sourceData['type'])
                        ->addFieldToFilter('name', $sourceData['name']);
                    if ($sourceCollection->count() === 1) {
                        unset($sourceData['new_source_id']);
                        Mage::getModel('xtento_stockimport/source')->setData($sourceData)->setId($sourceCollection->getFirstItem()->getId())->save();
                        $updatedCounter['sources']++;
                    } else {
                        $newSource = Mage::getModel('xtento_stockimport/source')->setData($sourceData);
                        if (isset($sourceData['new_source_id'])) {
                            $newSource->setId($sourceData['new_source_id']);
                            unset($sourceData['new_source_id']);
                            $newSource->saveWithId();
                        } else {
                            unset($sourceData['new_source_id']);
                            $newSource->save();
                        }
                        $addedCounter['sources']++;
                    }
                } else {
                    $newSource = Mage::getModel('xtento_stockimport/source')->setData($sourceData);
                    if (isset($sourceData['new_source_id'])) {
                        $newSource->setId($sourceData['new_source_id']);
                        unset($sourceData['new_source_id']);
                        $newSource->saveWithId();
                    } else {
                        unset($sourceData['new_source_id']);
                        $newSource->save();
                    }
                    $addedCounter['sources']++;
                }
            }
        }
        // Done
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_stockimport')->__('%d profiles have been added, %d profiles have been updated, %d sources have been added, %d sources have been updated.', $addedCounter['profiles'], $updatedCounter['profiles'], $addedCounter['sources'], $updatedCounter['sources']));
        return $this->_redirectReferer();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/stockimport')
            ->_title(Mage::helper('xtento_stockimport')->__('Stock Import'))->_title(Mage::helper('xtento_stockimport')->__('Tools'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/stockimport/tools');
    }

}