<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-06-17T15:59:13+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Product/Parent.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Product_Parent extends Xtento_ProductExport_Model_Export_Data_Product_General
{
    /**
     * Parent product cache
     */
    protected static $_parentProductCache = array();

    public function getConfiguration()
    {
        // Reset cache
        self::$_parentProductCache = array();

        return array(
            'name' => 'Parent item information',
            'category' => 'Product',
            'description' => 'Export parent item',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        // Fetch product - should be a child
        $product = $collectionItem->getProduct();

        if ($this->getProfile()->getOutputType() == 'xml') {
            return $returnArray;
        }

        $parentId = -1;
        // Check if it's a child product, and if yes, find & export parent id
        if ($this->fieldLoadingRequired('parent_id')) {
            $this->_writeArray = & $returnArray; // Write on product level
            $parentId = $this->_getFirstParentProductId($product);
            $this->writeValue('parent_id', $parentId);
        }

        if (!isset(self::$_parentProductCache[$this->getStoreId()])) {
            self::$_parentProductCache[$this->getStoreId()] = array();
        }

        // Find & export parent item
        if ($this->fieldLoadingRequired('parent_item') || $this->fieldLoadingRequired('option_parameters_in_url')) {
            $returnArray['parent_item'] = array();
            $this->_writeArray = & $returnArray['parent_item']; // Write on parent_item level
            if ($parentId == -1) {
                $parentId = $this->_getFirstParentProductId($product);
            }
            if ($parentId) {
                if (!array_key_exists($parentId, self::$_parentProductCache[$this->getStoreId()])) {
                    if ($this->getStoreId()) {
                        $parent = Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($parentId);
                    } else {
                        $parent = Mage::getModel('catalog/product')->load($parentId);
                    }
                    if ($parent && $parent->getId()) {
                        if ($this->fieldLoadingRequired('option_parameters_in_url')) {
                            $superAttributesWithValues = array();
                            $superAttributes = $parent->getTypeInstance(true)->getConfigurableAttributes($parent);
                            foreach ($superAttributes as $superAttribute) {
                                $superAttributeId = $superAttribute->getProductAttribute()->getId();
                                $superAttributeCode = $superAttribute->getProductAttribute()->getAttributeCode();
                                $superAttributeValues = $superAttribute->getPrices() ? $superAttribute->getPrices() : array();
                                foreach ($superAttributeValues as $superAttributeValue) {
                                    if ($superAttributeValue['value_index'] == $product->getData($superAttributeCode)) {
                                        $superAttributesWithValues[] = $superAttributeId . "=" . $superAttributeValue['value_index'];
                                    }
                                }
                            }
                            $this->writeValue('option_parameters_in_url', implode("&", $superAttributesWithValues));
                        }
                        // Export product data of parent product
                        $this->_exportProductData($parent, $returnArray['parent_item']);
                        $this->writeValue('entity_id', $parent->getId());
                        if ($this->fieldLoadingRequired('parent_item/cats')) {
                            // Export categories for parent product
                            $fakedCollectionItem = new Varien_Object();
                            $fakedCollectionItem->setProduct($parent);
                            $exportClass = Mage::getSingleton('xtento_productexport/export_data_product_categories');
                            $exportClass->setProfile($this->getProfile());
                            $exportClass->setShowEmptyFields($this->getShowEmptyFields());
                            $returnData = $exportClass->getExportData(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT, $fakedCollectionItem);
                            if (is_array($returnData) && !empty($returnData)) {
                                $this->_writeArray = array_merge_recursive($this->_writeArray, $returnData);
                            }
                        }
                    }
                    // Cache parent product
                    self::$_parentProductCache[$this->getStoreId()][$parentId] = $this->_writeArray;
                } else {
                    // Copy from cache
                    $this->_writeArray = self::$_parentProductCache[$this->getStoreId()][$parentId];
                }
            }
        }
        $this->_writeArray = & $returnArray; // Write on product level

        // Done
        return $returnArray;
    }

    /**
     * Get parent id of the product
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function _getFirstParentProductId($product)
    {
        $parentId = null;
        #if ($product->getTypeId() == 'simple') {
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            if (!$parentIds) {
                $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            }
            foreach ($parentIds as $possibleParentId) {
                // Check if parent product exists, if yes return first existing parent product
                $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                $select = $readAdapter->select()
                    ->from(Mage::getSingleton('core/resource')->getTableName('catalog/product'), array('entity_id'))
                    ->where("entity_id = ?", $possibleParentId);
                $products = $readAdapter->fetchAll($select);
                if (count($products) > 0) {
                    $parentId = $possibleParentId;
                }
            }
        #}

        return (int)$parentId;
    }
}