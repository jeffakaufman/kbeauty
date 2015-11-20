<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-03-12T15:41:43+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Entity/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_ProductExport_Model_Export_Entity_Abstract extends Mage_Core_Model_Abstract
{
    protected $_collection;
    private $_returnArray = array();

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _runExport()
    {
        if ($this->getProfile()->getEntity() == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT && $this->getProfile()->getStoreIds() !== '') {
            $this->_collection->addStoreFilter($this->getProfile()->getStoreIds());
        }
        // Reset export classes
        Mage::getSingleton('xtento_productexport/export_data')->resetExportClasses();
        // Backup original rule_data
        $origRuleData = Mage::registry('rule_data');
        $ruleDataChanged = false;
        // Register rule information for catalog rules
        $storeId = 0;
        if ($this->getProfile()->getStoreIds()) {
            $storeId = $this->getProfile()->getStoreIds();
        }
        $productStore = Mage::getModel('core/store')->load($storeId);
        if ($productStore) {
            Mage::unregister('rule_data');
            Mage::register('rule_data', new Varien_Object(array(
                'store_id' => $storeId,
                'website_id' => $productStore->getWebsiteId(),
                'customer_group_id' => $this->getProfile()->getCustomerGroupId() ? $this->getProfile()->getCustomerGroupId() : 0, // 0 = NOT_LOGGED_IN
            )));
            $ruleDataChanged = true;
        }
        // Get export fields
        $exportFields = array(); // Deprecated
        $originalCollection = $this->_collection;
        $collectionCount = null;
        $currItemNo = 1;
        $currPage = 1;
        $lastPage = 0;
        $break = false;
        #Mage::log("START: Memory Usage: " . round((memory_get_usage() / 1024 / 1024), 2) . "MB ", null, "debug_memory_xtento_product_export.log", true);
        #$prevMemoryUsage = 0;
        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize(100); // If just 100 items are returned with every export, something is wrong with getLastPageNumber()
            $collection->setCurPage($currPage);
            $collection->load();
            if (is_null($collectionCount)) {
                $collectionCount = $collection->getSize();
                $lastPage = $collection->getLastPageNumber();
            }
            if ($currPage == $lastPage) {
                $break = true;
            }
            $currPage++;
            #Mage::log("Page " . $currPage . " of " . $lastPage . "  memory Usage: " . round((memory_get_usage() / 1024 / 1024), 2) . "MB (delta: " . round(((memory_get_usage() - $prevMemoryUsage) / 1024 / 1024), 2) . "MB) ", null, "debug_memory_xtento_product_export.log", true);
            #$prevMemoryUsage = memory_get_usage();
            foreach ($collection as $collectionItem) {
                #var_dump("validation result: ".$this->getProfile()->validate($collectionItem));
                if ($this->getExportType() == Xtento_ProductExport_Model_Export::EXPORT_TYPE_TEST || $this->getProfile()->validate($collectionItem)) {
                    $returnData = $this->_exportData(new Xtento_ProductExport_Model_Export_Entity_Collection_Item($collectionItem, $this->_entityType, $currItemNo, $collectionCount), $exportFields);
                    if (!empty($returnData)) {
                        $this->_returnArray[] = $returnData;
                        $currItemNo++;
                    }
                }
            }
        }
        #Mage::log("DONE: Memory Usage: " . round((memory_get_usage() / 1024 / 1024), 2) . "MB ", null, "debug_memory_xtento_product_export.log", true);
        if ($ruleDataChanged) {
            Mage::unregister('rule_data');
            Mage::register('rule_data', $origRuleData);
        }
        #var_dump($this->_returnArray); die();
        return $this->_returnArray;
    }

    public function setCollectionFilters($filters)
    {
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                foreach ($filter as $attribute => $filterArray) {
                    $this->_collection->addAttributeToFilter($attribute, $filterArray);
                }
            }
        }
        return $this->_collection;
    }

    protected function _exportData($collectionItem, $exportFields)
    {
        return Mage::getSingleton('xtento_productexport/export_data')
            ->setShowEmptyFields($this->getShowEmptyFields())
            ->setProfile($this->getProfile() ? $this->getProfile() : new Varien_Object())
            ->setExportFields($exportFields)
            ->getExportData($this->_entityType, $collectionItem);
    }

    public function runExport()
    {
        return $this->_runExport();
    }
}