<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-02-27T18:36:13+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Product/General.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Product_General extends Xtento_ProductExport_Model_Export_Data_Abstract
{
    /**
     * Cache
     */
    protected static $_attributeSetCache = array();
    protected static $_mediaGalleryBackend = false;
    protected $_config = array();

    public function getConfiguration()
    {
        // Reset cache
        self::$_attributeSetCache = array();

        return array(
            'name' => 'General product information',
            'category' => 'Product',
            'description' => 'Export extended product information.',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray; // Write directly on product level
        // Fetch fields to export
        $product = $collectionItem->getProduct();

        if ($product->getTypeId() && $this->getProfile() && in_array($product->getTypeId(), explode(",", $this->getProfile()->getExportFilterProductType()))) {
            return $returnArray; // Product type should be not exported
        }

        // Timestamps of creation/update
        if ($this->fieldLoadingRequired('created_at_timestamp')) $this->writeValue('created_at_timestamp', Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($product->getCreatedAt()));
        if ($this->fieldLoadingRequired('updated_at_timestamp')) $this->writeValue('updated_at_timestamp', Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($product->getUpdatedAt()));

        // Which line is this?
        $this->writeValue('line_number', $collectionItem->_currItemNo);
        $this->writeValue('count', $collectionItem->_collectionSize);

        // Export information
        $this->writeValue('export_id', (Mage::registry('product_export_log')) ? Mage::registry('product_export_log')->getId() : 0);

        $this->_exportProductData($product, $returnArray);

        // Done
        return $returnArray;
    }

    /**
     * @param $product Mage_Catalog_Model_Product
     */
    protected function _exportProductData($product, &$returnArray)
    {
        if ($this->getStoreId()) {
            $product->setStoreId($this->getStoreId());
            $this->writeValue('store_id', $this->getStoreId());
        } else {
            $this->writeValue('store_id', 0);
        }

        if (!isset($this->_config['including_tax'])) {
            $this->_config['including_tax'] = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $product->getStore());
        }

        $exportAllFields = false;
        if ($this->getProfile()->getOutputType() == 'xml') {
            $exportAllFields = true;
        }

        #Zend_Debug::dump($product->getData()); die();
        foreach ($product->getData() as $key => $value) {
            if ($key == 'entity_id') {
                continue;
            }
            if ($key == 'price') {
                $this->writeValue('original_price', $value);
                continue;
            }
            if (!$this->fieldLoadingRequired($key)) {
                if ($this->fieldLoadingRequired($key . '_raw') && !$exportAllFields) {
                    $this->writeValue($key . '_raw', $value);
                }
                continue;
            }
            if ($key == 'cost') {
                $this->writeValue('cost', Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'cost', $this->getStoreId()));
                continue;
            }
            if ($key == 'min_price' || $key == 'max_price' || $key == 'special_price') {
                $value = $this->_addTax($product, $value, $key);
            }
            if ($key == 'qty') {
                $value = sprintf('%d', $value);
            }
            if ($key == 'image' || $key == 'small_image' || $key == 'thumbnail') {
                $this->writeValue($key . '_raw', $value);
                $this->writeValue($key, Mage::app()->getStore($this->getStoreId())->getBaseUrl('media', Mage::getStoreConfigFlag('web/secure/use_in_frontend', $this->getStoreId())) . 'catalog/product/' . ltrim($value, '/'));
                continue;
            }
            $attribute = $product->getResource()->getAttribute($key);
            if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
                $attribute->setStoreId($product->getStoreId());
            }
            #if ($key == 'test') {
            #    var_dump($product->getAttributeText($key), $attribute->getStoreLabel($product->getStore()), $attribute);
            #    die();
            #}
            $attrText = '';
            if ($attribute) {
                if ($attribute->getFrontendInput() === 'weee' || $attribute->getFrontendInput() === 'media_gallery') {
                    // Don't export certain frontend_input values
                    continue;
                }
                try {
                    $attrText = $product->getAttributeText($key);
                } catch (Exception $e) {
                    //echo "Problem with attribute $key: ".$e->getMessage();
                    continue;
                }
            }
            if (!empty($attrText)) {
                if (is_array($attrText)) {
                    // Multiselect:
                    foreach ($attrText as $index => $val) {
                        if (!is_array($index) && !is_array($val)) {
                            $this->writeValue($key . '_value_' . $index, $val);
                        }
                    }
                    $this->writeValue($key, implode(",", $attrText));
                } else {
                    if ($attribute->getFrontendInput() == 'multiselect') {
                        $this->writeValue($key . '_value_0', $attrText);
                    }
                    $this->writeValue($key, $attrText);
                }
            } else {
                $this->writeValue($key, $value);
            }
            if ($key == 'visibility' || $key == 'status' || $key == 'tax_class_id' || ($this->fieldLoadingRequired($key.'_raw') && !$exportAllFields)) {
                $this->writeValue($key . '_raw', $value);
            }
        }

        // Extended fields
        if ($this->fieldLoadingRequired('product_url')) {
            $productUrl = $product->getProductUrl(false);
            if ($this->getProfile()->getExportUrlRemoveStore()) {
                if (preg_match("/&/", $productUrl)) {
                    $productUrl = preg_replace("/___store=(.*?)&/", "&", $productUrl);
                } else {
                    $productUrl = preg_replace("/\?___store=(.*)/", "", $productUrl);
                }
            }
            $this->writeValue('product_url', $productUrl);
        }
        if ($this->fieldLoadingRequired('price')) {
            $this->writeValue('price', $this->_getPrice($product));
        }
        if ($this->fieldLoadingRequired('attribute_set_name')) {
            $attributeSetId = $product->getAttributeSetId();
            if (!array_key_exists($attributeSetId, self::$_attributeSetCache)) {
                $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);
                $attributeSetName = '';
                if ($attributeSet->getId()) {
                    $attributeSetName = $attributeSet->getAttributeSetName();
                    $this->writeValue('attribute_set_name', $attributeSetName);
                }
                self::$_attributeSetCache[$attributeSetId] = $attributeSetName;
            } else {
                $this->writeValue('attribute_set_name', self::$_attributeSetCache[$attributeSetId]);
            }
        }

        // Upsell product IDs / SKUs
        if ($this->fieldLoadingRequired('upsell_product_ids') && !$exportAllFields) {
            $this->writeValue('upsell_product_ids', implode(",", $product->getUpSellProductIds()));
        }
        if ($this->fieldLoadingRequired('upsell_product_skus') && !$exportAllFields) {
            $skus = array();
            foreach ($product->getUpSellProductCollection() as $upsellProduct) {
                $skus[] = $upsellProduct->getSku();
            }
            $this->writeValue('upsell_product_skus', implode(",", $skus));
        }
        // Cross-Sell product IDs / SKUs
        if ($this->fieldLoadingRequired('cross_sell_product_ids') && !$exportAllFields) {
            $this->writeValue('cross_sell_product_ids', implode(",", $product->getCrossSellProductIds()));
        }
        if ($this->fieldLoadingRequired('cross_sell_product_skus') && !$exportAllFields) {
            $skus = array();
            foreach ($product->getCrossSellProductCollection() as $crosssellProduct) {
                $skus[] = $crosssellProduct->getSku();
            }
            $this->writeValue('cross_sell_product_skus', implode(",", $skus));
        }
        // Related product IDs / SKUs
        if ($this->fieldLoadingRequired('related_product_ids') && !$exportAllFields) {
            $this->writeValue('related_product_ids', implode(",", $product->getRelatedProductIds()));
        }
        if ($this->fieldLoadingRequired('related_product_skus') && !$exportAllFields) {
            $skus = array();
            foreach ($product->getRelatedProductCollection() as $relatedProduct) {
                $skus[] = $relatedProduct->getSku();
            }
            $this->writeValue('related_product_skus', implode(",", $skus));
        }
        if ($this->fieldLoadingRequired('website_codes') && !$exportAllFields) {
            $websiteCodes = array();
            foreach ($product->getWebsiteIds() as $websiteId) {
                $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                $websiteCodes[$websiteCode] = $websiteCode;
            }
            $this->writeValue('website_codes', join(',', $websiteCodes));
        }
        // Check how often this product is in pending/processing orders
        if ($this->fieldLoadingRequired('qty_pending') && !$exportAllFields) {
            $orderCollection = Mage::getResourceModel('sales/order_collection');
            $orderCollection->getSelect()->joinLeft(array('ordered_items' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')), 'main_table.entity_id = ordered_items.order_id', array('ordered_items.product_id'));
            $orderCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('status', array('nin' => array('complete', 'closed')))
                ->addAttributeToFilter('ordered_items.product_id', array('eq' => $product->getId()));
            $orderCollection->getSelect()->group('main_table.entity_id');
            $processingQty = 0;
            foreach ($orderCollection as $order) {
                foreach ($order->getAllItems() as $item) {
                    if ($item->getProductId() === $product->getId()) {
                        $processingQty += $item->getQtyOrdered();
                    }
                }
            }
            $this->writeValue('qty_pending', $processingQty);
        }
        // Is special price active?
        if ($this->fieldLoadingRequired('special_price_active') && !$exportAllFields) {
            $dateToday = new Zend_Date(Mage::getSingleton('core/date')->date(), Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateToday->setHour(0)->setMinute(0)->setSecond(0);
            $isSpecialPriceActive = true;
            if ($product->getSpecialFromDate()) {
                $fromDate = new Zend_Date($product->getSpecialFromDate(), Varien_Date::DATETIME_INTERNAL_FORMAT);
                $fromDate->setHour(0)->setMinute(0)->setSecond(0);
                if ($dateToday->isEarlier($fromDate)) {
                    $isSpecialPriceActive = false;
                }
            } else {
                $isSpecialPriceActive = false;
            }
            if ($product->getSpecialToDate()) {
                $toDate = new Zend_Date($product->getSpecialToDate(), Varien_Date::DATETIME_INTERNAL_FORMAT);
                $toDate->setHour(0)->setMinute(0)->setSecond(0);
                if ($dateToday->isLater($toDate)) {
                    $isSpecialPriceActive = false;
                }
            } else {
                $isSpecialPriceActive = false;
            }
            $this->writeValue('special_price_active', (int)$isSpecialPriceActive);
        }

        if ($this->fieldLoadingRequired('images') && !$exportAllFields) {
            $returnArray['images'] = array();
            $originalWriteArray = & $this->_writeArray;
            $this->_writeArray = & $returnArray['images'];
            #$product->load('media_gallery');
            if (self::$_mediaGalleryBackend === false) {
                $attributes = $product->getTypeInstance(true)->getSetAttributes($product);
                if (isset($attributes['media_gallery'])) {
                    self::$_mediaGalleryBackend = $attributes['media_gallery']->getBackend();
                }
            }
            if (self::$_mediaGalleryBackend !== false) {
                self::$_mediaGalleryBackend->afterLoad($product);
                $mediaGalleryImages = $product->getMediaGalleryImages();
                foreach ($mediaGalleryImages as $mediaGalleryImage) {
                    $this->_writeArray = & $returnArray['images'][];
                    foreach ($mediaGalleryImage->getData() as $key => $value) {
                        $this->writeValue($key, $value);
                    }
                }
            }
            $this->_writeArray = & $originalWriteArray;
        }

        // Get custom options
        if ($this->fieldLoadingRequired('custom_options') && !$exportAllFields) {
            $returnArray['custom_options'] = array();
            $originalWriteArray = & $this->_writeArray;
            $this->_writeArray = & $returnArray['custom_options'];
            // Unfortunately you can only fetch custom options with the product being loaded. No way to add all the fields on collection load.
            $product->load($product->getId());
            $productOptions = $product->getOptions();
            if (is_array($productOptions)) {
                foreach ($productOptions as $productOption) {
                    $customOption = & $returnArray['custom_options'][];
                    $this->_writeArray = & $customOption;
                    foreach ($productOption->getData() as $key => $value) {
                        $this->writeValue($key, $value);
                    }
                    $optionValues = $productOption->getValues();
                    if (is_array($optionValues)) {
                        $this->_writeArray = & $customOption['values'];
                        foreach ($optionValues as $optionValue) {
                            $this->_writeArray = & $customOption['values'][];
                            foreach ($optionValue->getData() as $key => $value) {
                                $this->writeValue($key, $value);
                            }
                        }
                    }
                }
            }
            $this->_writeArray = & $originalWriteArray;
        }

        // Tier prices
        if ($this->fieldLoadingRequired('tier_prices') && !$exportAllFields) {
            $returnArray['tier_prices'] = array();
            $originalWriteArray = & $this->_writeArray;
            $this->_writeArray = & $returnArray['tier_prices'];
            $attribute = $product->getResource()->getAttribute('tier_price');

            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $tierPrices = $product->getData('tier_price');
                if (is_array($tierPrices)) {
                    foreach ($tierPrices as $tierPrice) {
                        $tierPriceNode = & $returnArray['tier_prices'][];
                        $this->_writeArray = & $tierPriceNode;
                        foreach ($tierPrice as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                    }
                }
            }
            $this->_writeArray = & $originalWriteArray;
        }
    }

    private function _getPrice($product)
    {
        $price = $product->getFinalPrice();
        if ($price == 0) {
            $price = $product->getMinPrice();
        }
        $price = $this->_addTax($product, $price, 'price');
        return $price;
    }

    private function _addTax($product, $price, $key)
    {
        $taxPercent = false;
        if ($product->getTaxPercent()) {
            $taxPercent = $product->getTaxPercent();
        } else {
            $taxPercent = false;
            if ($product->getTypeId() == 'grouped') {
                // Get tax_percent from child product
                $childProductIds = $product->getTypeInstance()->getChildrenIds($product->getId());
                if (is_array($childProductIds)) {
                    $childProductIds = array_shift($childProductIds);
                    if (is_array($childProductIds)) {
                        $childProductId = array_shift($childProductIds);
                        $childProduct = Mage::getModel('catalog/product')->load($childProductId);
                        if ($childProduct->getId()) {
                            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $product->getStore());
                            $taxPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($childProduct->getTaxClassId()));
                        }
                    }
                }
            }
        }
        if ($taxPercent > 0) {
            if (!$this->_config['including_tax']) {
                // Write price excl. tax
                $this->writeValue($key . '_excl_tax', $price);
                // Prices are excluding tax -> add tax
                $price *= 1 + $taxPercent / 100;
            } else {
                // Prices are including tax - do not add tax to price
                // Write price excl. tax
                $this->writeValue($key . '_excl_tax', $price / (1 + $taxPercent / 100));
            }
        } else {
            $this->writeValue($key . '_excl_tax', $price);
        }
        return $price;
    }
}