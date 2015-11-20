<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-01-09T21:40:11+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Product/Children.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Product_Children extends Xtento_ProductExport_Model_Export_Data_Product_General
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Child product information',
            'category' => 'Product',
            'description' => 'Export child products of configurable products',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $returnArray['child_products'] = array();

        // Fetch product - should be a "parent" item
        $product = $collectionItem->getProduct();
        if ($product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_GROUPED && $product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            return $returnArray;
        }

        $exportAllFields = false;
        if ($this->getProfile()->getOutputType() == 'xml') {
            $exportAllFields = true;
        }

        // Find & export child item
        if ($this->fieldLoadingRequired('child_products') && !$exportAllFields) {
            $originalWriteArray = & $this->_writeArray;
            $this->_writeArray = & $returnArray['child_products']; // Write on child_item level

            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $childProducts = $product->getTypeInstance(true)->getUsedProductCollection($product);
                if ($this->fieldLoadingRequired('child_price')) {
                    $childPrices = array();
                    $childAttributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                    // Loop all attributes and find out the pricing value - be aware that this could be percentage
                    foreach ($childAttributes as $childAttribute) {
                        if ($childAttribute->getPrices()) {
                            foreach ($childAttribute->getPrices() as $attributePrice) {
                                $childPrices[$attributePrice['value_index']] = $attributePrice['pricing_value'];
                            }
                        }
                    }
                }
            }
            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
                $childProducts = $product->getTypeInstance(true)->getAssociatedProductCollection($product);
            }
            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $childProducts = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
                $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
            }

            $childProducts->addAttributeToSelect('*');
            $childProducts->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
            $childProducts->addTaxPercents();
            if ($this->getProfile()->getStoreIds()) {
                $childProducts->getSelect()->joinLeft(Mage::getSingleton('core/resource')->getTableName('catalog_product_index_price') . ' AS price_index', 'price_index.entity_id=e.entity_id AND customer_group_id=0 AND  price_index.website_id=' . Mage::getModel('core/store')->load($this->getProfile()->getStoreIds())->getWebsiteId(), array('min_price' => 'min_price', 'max_price' => 'max_price', 'tier_price' => 'tier_price', 'final_price' => 'final_price'));
                $childProducts->addStoreFilter($this->getProfile()->getStoreIds());
                $childProducts->addAttributeToSelect("tax_class_id");
            }

            foreach ($childProducts as $childProduct) {
                $this->_writeArray = & $returnArray['child_products'][];
                if ($this->getStoreId()) {
                    $childProduct->setStoreId($this->getStoreId());
                }
                $this->_exportProductData($childProduct, $this->_writeArray);
                $this->writeValue('entity_id', $childProduct->getId());
                if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE && $this->fieldLoadingRequired('bundle_option')) {
                    $this->_writeArray['bundle_option'] = array();
                    $originalWriteArray = & $this->_writeArray;
                    $this->_writeArray = & $this->_writeArray['bundle_option'];
                    $bundleOption = $optionCollection->getItemById($childProduct->getOptionId());
                    if ($bundleOption->getId()) {
                        foreach ($bundleOption->getData() as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                    $this->_writeArray = & $originalWriteArray;
                }
                if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $this->fieldLoadingRequired('child_price')) {
                    $childExtraPrice = 0;
                    foreach ($childAttributes as $childAttribute) {
                        $value = $childProduct->getData($childAttribute->getProductAttribute()->getAttributeCode());
                        foreach ($childPrices as $priceKey => $priceValue) {
                            if ($priceKey == $value) {
                                $childExtraPrice += $priceValue;
                            }
                        }
                    }
                    $this->writeValue('child_price', $product->getFinalPrice() + $childExtraPrice);
                }
                if ($this->fieldLoadingRequired('child_products/cats')) {
                    // Export categories for child product
                    $fakedCollectionItem = new Varien_Object();
                    $fakedCollectionItem->setProduct($childProduct);
                    $exportClass = Mage::getSingleton('xtento_productexport/export_data_product_categories');
                    $exportClass->setProfile($this->getProfile());
                    $exportClass->setShowEmptyFields($this->getShowEmptyFields());
                    $returnData = $exportClass->getExportData(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT, $fakedCollectionItem);
                    if (is_array($returnData) && !empty($returnData)) {
                        $this->_writeArray = array_merge_recursive($this->_writeArray, $returnData);
                    }
                }
            }
            $this->_writeArray = & $originalWriteArray;
        }
        $this->_writeArray = & $returnArray; // Write on product level

        // Done
        return $returnArray;
    }
}