<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-04-29T19:48:23+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Custom/Product/AitocQuantityManager.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Custom_Product_AitocQuantityManager extends Xtento_ProductExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Aitoc Quantity Manager (Multi-Warehouse) Qty Export',
            'category' => 'Product',
            'description' => 'Export qty for website_id if Aitoc Quantity Manager is installed.',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
            'third_party' => true,
            'depends_module' => 'Aitoc_Aitquantitymanager',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        if (!$this->fieldLoadingRequired('aitquantitymanager')) {
            return $returnArray;
        }
        $this->_writeArray = & $returnArray['aitquantitymanager']; // Write on "aitquantitymanager" level
        // Fetch fields to export
        $product = $collectionItem->getProduct();

        try {
            if ($this->getProfile()->getStoreIds() != '') {
                $store = Mage::app()->getStore($this->getProfile()->getStoreIds());
                $websiteId = $store->getWebsiteId();
            } else {
                $websiteId = 0;
            }
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product, $websiteId);
            if ($stock->getId()) {
                foreach ($stock->getData() as $key => $value) {
                    if (!$this->fieldLoadingRequired($key)) {
                        continue;
                    }
                    if ($key == 'qty') {
                        $value = sprintf('%d', $value);
                    }
                    $this->writeValue($key, $value);
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}