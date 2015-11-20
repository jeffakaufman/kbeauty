<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2015-03-13T22:34:31+01:00
 * File:          app/code/local/Xtento/StockImport/Model/Import/Entity/Stock.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Import_Entity_Stock extends Xtento_StockImport_Model_Import_Entity_Abstract
{
    /*
     * Enable price import functionality? Beta currently.
     */
    static $importStock = true;
    static $importPrices = true;
    static $importSpecialPrices = false;
    static $importCost = false;
    static $importStockId = false;
    static $importProductStatus = false;
    static $maxImportFilterCount = 3;

    /*
     * Attribute to identify stock items by
     */
    protected $_attributeToLoadBy = 'sku';
    /*
     * Product identifiers (could be the SKU, an attribute, ... - attribute loaded by defined in function getProductIdsForProductIdentifiers)
     */
    protected $_productIdentifiers = array();
    /*
     * Associative array holding productIdentifer => product_id
     */
    protected $_productMap = array();
    protected $_productTypeMap = array();
    /*
     * Products not found in Magento
     */
    protected $_productsNotFound = array();
    /*
     * Existing stock_items taken directly from the cataloginventory_stock_item table.
     */
    protected $_stockItems = array();
    /*
     * Existing stock_status items taken directly from the cataloginventory_stock_status table.
     */
    protected $_stockStatusItems = array();
    /*
     * Which stock_items have been modified? Important for re-index
     */
    protected $_modifiedStockItems = array();
    /*
     * Current prices for products
     */
    protected $_prices = array();
    protected $_specialPrices = array();
    protected $_costValues = array();
    /*
     * Updated prices
     */
    protected $_updatedPrices = array();
    protected $_updatedSpecialPrices = array();
    protected $_updatedCostValues = array();
    /*
     * Current product status
     */
    protected $_productStatus = array();
    protected $_updatedProductStatuses = array();

    /*
     * Prepare import by getting a mapping of the attribute used to identify the product and its product id
     */
    public function prepareImport($updatesInFilesToProcess)
    {
        // Reset stock
        /*$this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_item'),
            array('qty' => 0, 'is_in_stock' => 0)
        );
        $this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_status'),
            array('qty' => 0, 'stock_status' => 0)
        );*/

        // Reset stock for specific product IDs
        /*$productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToFilter('supplier', 'techdata');
        $productIds = $productCollection->getAllIds();
        $this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_item'),
            array('qty' => 0, 'is_in_stock' => 0),
            "product_id IN (" . join(",", $productIds) . ")"
        );
        $this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_status'),
            array('qty' => 0, 'stock_status' => 0),
            "product_id IN (" . join(",", $productIds) . ")"
        );*/

        // @todo: When importing duplicate SKUs from multiple files, the import may fail, as it thinks it needs to insert the stock item/status entry again. Refresh stock item/status tables after every file processed.
        // Prepare product identifiers
        $this->_getProductIdentifiers($updatesInFilesToProcess);
        if (empty($this->_productIdentifiers)) {
            $this->getLogEntry()->addDebugMessage('No products could be found in the import file.');
            return false;
        }

        // Get product IDs for product identifiers
        $this->_getProductIdsForProductIdentifiers();
        if (empty($this->_productMap)) {
            $this->getLogEntry()->addDebugMessage('The products supplied in the import file could not be found in the Magento catalog.');
            return false;
        }

        $this->_applyFiltersToFoundProducts();
        if (empty($this->_productMap)) {
            $this->getLogEntry()->addDebugMessage('The products supplied in the import file could not be found in the Magento catalog OR all were filtered by profile filters.');
            return false;
        }

        // Find out which products couldn't be found in Magento
        foreach ($this->_productIdentifiers as $productIdentifier) {
            if (!isset($this->_productMap[$productIdentifier])) {
                array_push($this->_productsNotFound, $productIdentifier);
            }
        }

        // Set all product IDs not in files to out of stock
        /*$this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_item'),
            array('qty' => 0, 'is_in_stock' => 0),
            "product_id NOT IN (" . join(",", $this->_productMap) . ")"
        );
        $this->_writeAdapter->update(
            $this->getTableName('cataloginventory/stock_status'),
            array('qty' => 0, 'stock_status' => 0),
            "product_id NOT IN (" . join(",", $this->_productMap) . ")"
        );*/

        // Which fields are in the file and should be handled for the import?
        $fieldsFound = array();
        foreach ($updatesInFilesToProcess as $updatesInFile) {
            if (isset($updatesInFile['FIELDS'])) {
                foreach ($updatesInFile['FIELDS'] as $field) {
                    $fieldsFound[] = $field;
                }
            }
        }
        if (!in_array('qty', $fieldsFound) && !in_array('is_in_stock', $fieldsFound) && !in_array('manage_stock', $fieldsFound) && !in_array('notify_stock_qty', $fieldsFound)) {
            self::$importStock = false;
        }
        if (!in_array('price', $fieldsFound)) {
            self::$importPrices = false;
        }
        if (!in_array('special_price', $fieldsFound)) {
            self::$importSpecialPrices = false;
        }
        if (!in_array('cost', $fieldsFound)) {
            self::$importCost = false;
        }
        if (!in_array('status', $fieldsFound)) {
            self::$importProductStatus = false;
        }
        if (Mage::helper('xtento_stockimport/import')->getMultiWarehouseSupport()) {
            self::$importStockId = true;
        }

        // Proceed with gathering information required for the import
        if (self::$importStock) {
            // Get current stock info - so what exists in the stock tables and what doesn't
            $this->_getCurrentStockInfo();
        }

        if (self::$importPrices || self::$importSpecialPrices || self::$importCost) {
            // If price import is enabled.. get price info
            $this->_getCurrentPriceInfo();
        }
        if (self::$importPrices || self::$importSpecialPrices) {
            // If price import is enabled.. set indexer to manual mode.
            if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
                foreach ($processes as $indexerProcess) {
                    if (!in_array($indexerProcess->getIndexerCode(), array('catalog_product_price', 'catalog_product_flat', 'catalog_category_product'))) {
                        continue;
                    }
                    $indexerProcess->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
                }
            }
        }
        if (self::$importProductStatus) {
            $this->_getCurrentProductInfo();
        }

        // Start transaction for all the updates.. performance is the key here!
        #$this->_writeAdapter->query('LOCK TABLES '.$this->getTableName('cataloginventory/stock_status').' WRITE');
        #$this->_writeAdapter->query('LOCK TABLES '.$this->getTableName('cataloginventory/stock_item').' WRITE');

        // Only start a transaction if no product data is updated:
        if (!self::$importPrices && !self::$importSpecialPrices && !self::$importCost && !self::$importProductStatus) {
            $this->_writeAdapter->beginTransaction();
        }

        $this->getLogEntry()->addDebugMessage('Transaction started. Starting import.');
        return true;
    }

    /*
     * Get all the product identifiers we're supposed to identify stock items by. Could be the SKU or an attribute.
     */
    private function _getProductIdentifiers($updatesInFilesToProcess)
    {
        $this->_productIdentifiers = array();
        foreach ($updatesInFilesToProcess as $updateFile) {
            foreach ($updateFile['ITEMS'] as $stockId => $updatesInFile) {
                foreach ($updatesInFile as $productIdentifier => $updateData) {
                    /*if (is_numeric($productIdentifier)) {
                        $productIdentifier = "'" . trim($productIdentifier) . "'";
                    } else {*/
                    $productIdentifier = trim($productIdentifier);
                    /*}*/
                    array_push($this->_productIdentifiers, strtolower($productIdentifier));
                }
            }
        }
        return $this->_productIdentifiers;
    }

    /*
     * Get product ids for stock items based on the product identifiers supplied
     */
    private function _getProductIdsForProductIdentifiers()
    {
        // Which attribute is supposed be the identifier in the import file for the mapping to the actual products in Magento?
        if ($this->getConfig('product_identifier') == 'sku') {
            $this->_attributeToLoadBy = 'sku';
        } else if ($this->getConfig('product_identifier') == 'attribute') {
            $this->_attributeToLoadBy = $this->getConfig('product_identifier_attribute_code');
        } else if ($this->getConfig('product_identifier') == 'entity_id') {
            $this->_attributeToLoadBy = 'entity_id';
        } else {
            Mage::throwException('Stock import: Attribute to use for identifying products not defined.');
        }

        if ($this->_attributeToLoadBy == 'sku') {
            $select = $this->_readAdapter->select()
                ->from($this->getTableName('catalog/product'), array('entity_id', 'type_id', 'sku'))
                ->where("LOWER(sku) in (" . $this->_readAdapter->quote($this->_productIdentifiers) . ")");
            $products = $this->_readAdapter->fetchAll($select);

            foreach ($products as $product) {
                /*if ($product['type_id'] == 'configurable' || $product['type_id'] == 'downloadable') {
                    continue;
                }*/
                $this->_productMap[trim(strtolower($product['sku']))] = $product['entity_id'];
                $this->_productTypeMap[$product['entity_id']] = $product['type_id'];
            }

            $productsNotFound = array();
            foreach ($this->_productIdentifiers as $productIdentifier) {
                if (!isset($this->_productMap[$productIdentifier])) {
                    array_push($productsNotFound, $productIdentifier);
                }
            }
            if (!empty($productsNotFound)) {
                $this->getLogEntry()->addDebugMessage('The following SKUs defined in the import file could not be found in the catalog: ' . join(", ", $productsNotFound));
                #mail(Mage::helper('xtento_stockimport')->getDebugEmail(), 'Magento Stock Import Module @ ' . @$_SERVER['SERVER_NAME'], 'Stock Import products not found: ' . join(", ", $productsNotFound));
            }

            unset($products, $select);
        } else if ($this->getConfig('product_identifier') == 'attribute') {
            // Check if attribute exists
            $entityType = Mage::getModel('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
            $eavAttribute = Mage::getModel('eav/entity_attribute')->getCollection()
                ->addFieldToFilter('entity_type_id', $entityType->getId())
                ->addFieldToFilter('attribute_code', $this->_attributeToLoadBy)
                ->getFirstItem();
            if (!$eavAttribute || !$eavAttribute->getId()) {
                Mage::throwException("The supplied product attribute code used to identify products does not exist.");
            }

            // Load product collection
            $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect($this->_attributeToLoadBy)
                ->addAttributeToFilter($this->_attributeToLoadBy, array('in' => str_replace("'", "", $this->_productIdentifiers)));

            foreach ($productCollection as $product) {
                $attrValue = $product->getData($this->_attributeToLoadBy);
                $attrValue = trim(strtolower($attrValue));
                $this->_productMap[$attrValue] = $product->getId();
                $this->_productTypeMap[$product->getId()] = $product->getTypeId();
            }
            unset($productCollection);
        } else if ($this->getConfig('product_identifier') == 'entity_id') {
            // We're supposed to use the entity_id to identify products.. that's great. Just use the IDs to load from tables etc. then!
            $select = $this->_readAdapter->select()
                ->from($this->getTableName('catalog/product'), array('entity_id', 'type_id', 'sku'))
                ->where("entity_id in (" . $this->_readAdapter->quote($this->_productIdentifiers) . ")");
            $products = $this->_readAdapter->fetchAll($select);

            foreach ($products as $product) {
                if ($product['type_id'] == 'configurable' || $product['type_id'] == 'downloadable') {
                    continue;
                }
                $this->_productMap[$product['entity_id']] = $product['entity_id'];
                $this->_productTypeMap[$product['entity_id']] = $product['type_id'];
            }

            $productsNotFound = array();
            foreach ($this->_productIdentifiers as $productIdentifier) {
                if (!isset($this->_productMap[$productIdentifier])) {
                    array_push($productsNotFound, $productIdentifier);
                }
            }
            if (!empty($productsNotFound)) {
                $this->getLogEntry()->addDebugMessage('The following product IDs defined in the import file could not be found in the catalog: ' . join(", ", $productsNotFound));
                #mail(Mage::helper('xtento_stockimport')->getDebugEmail(), 'Magento Stock Import Module @ ' . @$_SERVER['SERVER_NAME'], 'Stock Import products not found: ' . join(", ", $productsNotFound));
            }

            unset($products, $select);
        }
    }

    private function _applyFiltersToFoundProducts()
    {
        $profileConfig = $this->getProfile()->getConfiguration();
        for ($i = 1; $i <= self::$maxImportFilterCount; $i++) {
            if (!isset($profileConfig['import_filter_' . $i])) {
                continue;
            }
            $filter = $profileConfig['import_filter_' . $i];
            if (!array_key_exists('filter', $filter) ||
                !array_key_exists('attribute', $filter) ||
                !array_key_exists('condition', $filter) ||
                !array_key_exists('value', $filter)
            ) {
                $this->getLogEntry()->addDebugMessage('Warning: Filter ' . $i . ' has not been configured properly. Filter skipped.');
                continue;
            }
            if ($filter['filter'] == '' &&
                $filter['attribute'] == '' &&
                $filter['condition'] == '' &&
                $filter['value'] == ''
            ) {
                // Filter has not been set up - skip it.
                continue;
            }
            if ($filter['filter'] == '' ||
                $filter['attribute'] == '' ||
                $filter['condition'] == '' ||
                $filter['value'] == ''
            ) {
                $this->getLogEntry()->addDebugMessage('Warning: Filter ' . $i . ' has not been configured properly. One more multiple filter fields are empty. Filter skipped.');
                continue;
            }
            // Load products affected by filter, and "combine" products found + filter products so filter is applied.
            $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('entity_id');
            // Determine product attribute to filter by, handle dropdown attributes
            $eavAttribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $filter['attribute']);
            if (!$eavAttribute || !$eavAttribute->getId()) {
                $this->getLogEntry()->addDebugMessage('Warning: Filter ' . $i . ' uses a product attribute to filter which does not exist anymore. Filter skipped.');
                continue;
            }
            if ($eavAttribute->getFrontendInput() == 'select') {
                $dropdownId = null;
                $attributeOptions = $eavAttribute->getSource()->getAllOptions();
                foreach ($attributeOptions as $option) {
                    if (strcasecmp($option['label'], $filter['value']) == 0 || $option['value'] == $filter['value']) {
                        $dropdownId = $option['value'];
                    }
                }
                if ($dropdownId === null) {
                    $this->getLogEntry()->addDebugMessage('Warning: Filter ' . $i . ' tries to filter by a dropdown attribute value which does not exist. Please check the attribute "' . $filter['attribute'] . '" and make sure the exact dropdown option ("' . $filter['value'] . '") exists as an attribute option (Store View = Admin).');
                    continue;
                } else {
                    $filter['value'] = $dropdownId;
                }
            } else {
                if ($filter['condition'] == 'like' || $filter['condition'] == 'nlike') {
                    $filter['value'] = '%' . $filter['value'] . '%';
                }
            }
            if ($filter['condition'] == 'neq') {
                $productCollection->addAttributeToFilter(
                    $filter['attribute'],
                    array(
                        array($filter['condition'] => $filter['value']),
                        array('null' => true)
                    ),
                    'left'
                ); // Left join is required, so attribute values when joining attributes which don't have values which then are NULL can be checked
            } else {
                $productCollection->addAttributeToFilter(
                    $filter['attribute'],
                    array($filter['condition'] => $filter['value'])
                );
            }
            #echo (string)$productCollection->getSelect(); die();
            $foundProductIds = $productCollection->getAllIds();
            $removedProducts = 0;
            if ($filter['filter'] == 'include_only') {
                foreach ($this->_productMap as $productIdentifier => $productId) {
                    if (!in_array($productId, $foundProductIds)) {
                        unset($this->_productMap[$productIdentifier]);
                        $removedProducts++;
                    }
                }
            }
            if ($filter['filter'] == 'exclude') {
                foreach ($this->_productMap as $productIdentifier => $productId) {
                    if (in_array($productId, $foundProductIds)) {
                        unset($this->_productMap[$productIdentifier]);
                        $removedProducts++;
                    }
                }
            }
            $this->getLogEntry()->addDebugMessage('Filter ' . $i . ' has removed/filtered ' . $removedProducts . ' product(s) from the import files.');
        }
        #die();
    }

    /*
     * Get information about current stock settings, only for products we want to update though
     */
    private function _getCurrentStockInfo()
    {
        // Get stock_item information
        $select = $this->_readAdapter->select()
            ->from($this->getTableName('cataloginventory/stock_item'), array('product_id', 'qty', 'is_in_stock', 'stock_id', 'manage_stock', 'notify_stock_qty', 'use_config_manage_stock', 'min_qty', 'use_config_min_qty'))
            ->where("product_id in (" . join(",", array_values($this->_productMap)) . ")");
        $stockItems = $this->_readAdapter->fetchAll($select);

        foreach ($stockItems as $stockItem) {
            // Prepare qty field
            $stockItem['qty'] = sprintf('%.4f', $stockItem['qty']);
            $this->_stockItems[$stockItem['stock_id']][$stockItem['product_id']] = $stockItem;
        }

        // Get stock_status information
        $select = $this->_readAdapter->select()
            ->from($this->getTableName('cataloginventory/stock_status'), array('product_id', 'qty', 'stock_status', 'stock_id'))
            ->where("product_id in (" . join(",", array_values($this->_productMap)) . ")");
        $stockStatusItems = $this->_readAdapter->fetchAll($select);

        foreach ($stockStatusItems as $stockStatusItem) {
            // Prepare qty field
            $stockStatusItem['qty'] = sprintf('%.4f', $stockStatusItem['qty']);
            $this->_stockStatusItems[$stockStatusItem['stock_id']][$stockStatusItem['product_id']] = $stockStatusItem;
        }
    }

    /*
     * Get information about the current price levels for the products in the import file
     */
    private function _getCurrentPriceInfo()
    {
        if (self::$importPrices) {
            $priceAttributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
            if ($priceAttributeId) {
                $select = $this->_readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $priceAttributeId)
                    ->where("entity_id in (" . join(",", array_values($this->_productMap)) . ")");
                $configPriceUpdateStoreId = $this->getConfig('price_update_store_id');
                if (!empty($configPriceUpdateStoreId)) {
                    $storeIds = join(",", $configPriceUpdateStoreId);
                    if (!empty($storeIds)) {
                        $select->where("store_id in (" . $storeIds . ")");
                    }
                }
                $currentPrices = $this->_readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    $this->_prices[$currentPrice['entity_id']] = array('price' => sprintf('%.4f', $currentPrice['value']));
                }
            } else {
                Mage::throwException('Error while trying to get current price info. The price attribute could not be found.');
            }
        }
        if (self::$importSpecialPrices) {
            $priceAttributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'special_price');
            if ($priceAttributeId) {
                $select = $this->_readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $priceAttributeId)
                    ->where("entity_id in (" . join(",", array_values($this->_productMap)) . ")");
                $configPriceUpdateStoreId = $this->getConfig('price_update_store_id');
                if (!empty($configPriceUpdateStoreId)) {
                    $storeIds = join(",", $configPriceUpdateStoreId);
                    if (!empty($storeIds)) {
                        $select->where("store_id in (" . $storeIds . ")");
                    }
                }
                $currentPrices = $this->_readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    if (!empty($currentPrice['value'])) {
                        $this->_specialPrices[$currentPrice['entity_id']] = array('special_price' => sprintf('%.4f', $currentPrice['value']));
                    } else {
                        $this->_specialPrices[$currentPrice['entity_id']] = array('special_price' => '');
                    }
                }
            } else {
                Mage::throwException('Error while trying to get current special price info. The special price attribute could not be found.');
            }
        }
        if (self::$importCost) {
            $costAttributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'cost');
            if ($costAttributeId) {
                $select = $this->_readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $costAttributeId)
                    ->where("entity_id in (" . join(",", array_values($this->_productMap)) . ")");
                $currentPrices = $this->_readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    if (!empty($currentPrice['value'])) {
                        $this->_costValues[$currentPrice['entity_id']] = array('cost' => sprintf('%.4f', $currentPrice['value']));
                    } else {
                        $this->_costValues[$currentPrice['entity_id']] = array('cost' => '');
                    }
                }
            } else {
                self::$importCost = false;
                #Mage::throwException('Error while trying to get current "cost" info. The cost attribute could not be found.');
            }
        }
        #var_dump($this->_prices);
        #die();
    }

    /*
     * Get information about the current price levels for the products in the import file
     */
    private function _getCurrentProductInfo()
    {
        if (self::$importProductStatus) {
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'status');
            if ($attributeId) {
                $select = $this->_readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_int'))
                    ->where('attribute_id = ?', $attributeId)
                    ->where("entity_id in (" . join(",", array_values($this->_productMap)) . ")");
                $products = $this->_readAdapter->fetchAll($select);

                foreach ($products as $product) {
                    $this->_productStatus[$product['entity_id']] = array('status' => $product['value']);
                }
            }
        }
    }

    /*
     * Update stock level for product
     */
    public function processItem($productIdentifier, $updateData)
    {
        // Result (and debug information) returned to observer
        $importResult = array('error' => 'Nothing happened yet.');

        if (isset($updateData['product_identifier'])) unset($updateData['product_identifier']);
        $productIdentifier = strtolower($productIdentifier);

        if (!isset($updateData['stock_id']) || empty($updateData['stock_id'])) {
            $updateData['stock_id'] = 1;
        } else {
            $updateData['stock_id'] = intval($updateData['stock_id']);
        }

        // Update stock_item, stock_status and eventually the price
        if (isset($this->_productMap[$productIdentifier])) {
            $productId = $this->_productMap[$productIdentifier];
            // Current import result.. nothing has changed yet
            $importResult = array('changed' => false, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' was found in Magento, but no fields have changed compared to the current settings. Identified product ID is %s.", $productIdentifier, $productId));

            // Adjust stock level by pending/processing orders
            /*if (isset($updateData['qty'])) {
                $orderItemCollection = Mage::getModel('sales/order_item')->getCollection();
                $orderItemCollection
                    ->getSelect()
                    ->joinInner(
                        array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                        'order.entity_id = main_table.order_id'
                    )
                    ->where('main_table.product_id=?', $productId)
                    ->where('order.status="pending" or order.status="processing"');
                if ($orderItemCollection->count() > 0) {
                    $blockedQty = 0;
                    foreach ($orderItemCollection as $orderItem) {
                        $blockedQty += (int)$orderItem->getQtyOrdered();
                    }
                    $updateData['qty'] = $updateData['qty'] - $blockedQty;
                }
                #var_dump($orderItemCollection->count(), $productId, $updateData['qty'], $blockedQty); die();
            }*/
            // End stock level adjustment by pending/processing orders

            // Fetch updated fields
            $updatedFields = $this->_getUpdatedFields($updateData, $productId); // See if anything has changed..

            if (self::$importStock) {
                // Check is supported product type
                $productType = $this->_productTypeMap[$productId];
                if ($productType !== Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && $productType !== 'downloadable') {
                    // stock_item routine:
                    if (isset($this->_stockItems[$updateData['stock_id']][$productId])) {
                        // This is a known stock_item entry
                        if (!empty($updatedFields)) {
                            // Something has changed, update stock item
                            $importResult = $this->_updateStockItem($productId, $productIdentifier, $updatedFields, $updateData);
                            // Push product ID to modified stock items.
                            array_push($this->_modifiedStockItems, $productId);
                        } else {
                            // Nothing has changed
                            if ($this->getTestMode()) {
                                #return $importResult;
                            }
                        }
                    } else {
                        // New stock item, insert stock item
                        $importResult = $this->_insertStockItem($productId, $productIdentifier, $updateData);
                        // Push product ID to modified stock items.
                        array_push($this->_modifiedStockItems, $productId);
                    }

                    // stock_status routine, update only when not in test_mode
                    if (!$this->getTestMode()) {
                        if (isset($this->_stockStatusItems[$updateData['stock_id']][$productId])) {
                            // This is a known stock_status entry
                            if (!empty($updatedFields)) {
                                // Something has changed, update stock_status
                                $this->_updateStockStatus($productId, $updatedFields, $updateData);
                                // Push product ID to modified stock items.
                                if (!isset($this->_modifiedStockItems[$productId])) {
                                    array_push($this->_modifiedStockItems, $productId);
                                }
                            }
                        } else {
                            // New stock item, insert stock item
                            $this->_insertStockStatus($productId, $updateData);
                            // Push product ID to modified stock items.
                            if (!isset($this->_modifiedStockItems[$productId])) {
                                array_push($this->_modifiedStockItems, $productId);
                            }
                        }
                    }
                }
            }

            // If using the Aheadworks Product Updates extension, uncomment the following line:
            // Mage::getModel('productupdates/productupdates')->setProductupdatesToSend($productId);

            $productFieldsUpdated = false;

            $priceUpdateStoreId = array(Mage_Core_Model_App::ADMIN_STORE_ID);
            $configPriceUpdateStoreId = $this->getConfig('price_update_store_id');
            if ($configPriceUpdateStoreId !== '' && !empty($configPriceUpdateStoreId)) {
                $priceUpdateStoreId = $configPriceUpdateStoreId;
            }

            // price update routine
            if (self::$importPrices) {
                if (!empty($updateData) && isset($updateData['price'])) {
                    $newPrice = sprintf('%.4f', $updateData['price']);
                    if (isset($this->_prices[$productId])) {
                        $currentPrice = $this->_prices[$productId]['price'];
                        if ($currentPrice !== $newPrice) {
                            if (!$this->getTestMode()) {
                                foreach ($priceUpdateStoreId as $updateStoreId) {
                                    if ($updateStoreId == 0) {
                                        $updateStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
                                    }
                                    Mage::getSingleton('catalog/product_action')
                                        ->updateAttributes(array($productId), array('price' => $newPrice), $updateStoreId);
                                }
                            }
                            // Price has changed.
                            array_push($this->_updatedPrices, $productId);
                            $productFieldsUpdated['price'] = $newPrice;
                        }
                    } else {
                        if (!$this->getTestMode()) {
                            foreach ($priceUpdateStoreId as $updateStoreId) {
                                if ($updateStoreId == 0) {
                                    $updateStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
                                }
                                Mage::getSingleton('catalog/product_action')
                                    ->updateAttributes(array($productId), array('price' => $newPrice), $updateStoreId);
                            }
                        }
                        // Price has changed.
                        array_push($this->_updatedPrices, $productId);
                        $productFieldsUpdated['price'] = $newPrice;
                    }
                }
            }

            if (self::$importSpecialPrices && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['special_price'])) {
                    if (isset($this->_specialPrices[$productId])) {
                        $currentPrice = $this->_specialPrices[$productId]['special_price'];
                    } else {
                        $currentPrice = null;
                    }
                    if ($updateData['special_price'] != '') {
                        $newPrice = sprintf('%.4f', $updateData['special_price']);
                    } else {
                        $newPrice = '';
                    }
                    $fromDate = strftime('%Y-%m-%d');
                    if ($newPrice === "0.0000") {
                        $newPrice = "";
                        $fromDate = "";
                    }
                    if ($currentPrice !== $newPrice || $newPrice == '') {
                        foreach ($priceUpdateStoreId as $updateStoreId) {
                            if ($updateStoreId == 0) {
                                $updateStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
                            }
                            Mage::getSingleton('catalog/product_action')
                                ->updateAttributes(array($productId), array('special_price' => $newPrice), $updateStoreId);
                            Mage::getSingleton('catalog/product_action')
                                ->updateAttributes(array($productId), array('special_from_date' => $fromDate), $updateStoreId);
                        }
                        // Special price has changed.
                        array_push($this->_updatedSpecialPrices, $productId);
                        $productFieldsUpdated['special_price'] = $newPrice;
                        $productFieldsUpdated['special_from_date'] = $fromDate;
                    }
                } else {
                    if (isset($this->_specialPrices[$productId])) {
                        if (!empty($this->_specialPrices[$productId]['special_price'])) {
                            foreach ($priceUpdateStoreId as $updateStoreId) {
                                if ($updateStoreId == 0) {
                                    $updateStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
                                }
                                Mage::getSingleton('catalog/product_action')
                                    ->updateAttributes(array($productId), array('special_price' => ''), $updateStoreId);
                                Mage::getSingleton('catalog/product_action')
                                    ->updateAttributes(array($productId), array('special_from_date' => ''), $updateStoreId);
                            }
                            array_push($this->_updatedSpecialPrices, $productId);
                            $productFieldsUpdated['special_price'] = '';
                            $productFieldsUpdated['special_from_date'] = '';
                        }
                    }
                }
            }

            if (self::$importCost && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['cost'])) {
                    if (isset($this->_costValues[$productId])) {
                        $currentPrice = $this->_costValues[$productId]['cost'];
                        if ($updateData['cost'] != '') {
                            $newPrice = sprintf('%.4f', $updateData['cost']);
                        } else {
                            $newPrice = '';
                        }
                        if ($currentPrice !== $newPrice || $newPrice == '') {
                            Mage::getSingleton('catalog/product_action')
                                ->updateAttributes(array($productId), array('cost' => $newPrice), Mage_Core_Model_App::ADMIN_STORE_ID);
                            // Cost has changed.
                            array_push($this->_updatedCostValues, $productId);
                            $productFieldsUpdated['cost'] = $newPrice;
                        }
                    }
                } else {
                    if (isset($this->_costValues[$productId])) {
                        if (!empty($this->_costValues[$productId]['cost'])) {
                            Mage::getSingleton('catalog/product_action')
                                ->updateAttributes(array($productId), array('cost' => ''), Mage_Core_Model_App::ADMIN_STORE_ID);
                            $productFieldsUpdated['cost'] = '';
                        }
                    }
                }
            }

            if (self::$importProductStatus && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['status'])) {
                    if (isset($this->_productStatus[$productId])) {
                        $currentStatus = $this->_productStatus[$productId]['status'];
                        if ($updateData['status'] != '') {
                            $updateStatus = strtolower($updateData['status']);
                            $newStatus = null;
                            if ($updateStatus == 'yes' || $updateStatus == '1' || $updateStatus == 'enabled' || $updateStatus == 'ja' || $updateStatus == 'true') {
                                $newStatus = 1;
                            }
                            if ($updateStatus == 'no' || $updateStatus == '0' || $updateStatus == 'disabled' || $updateStatus == 'nein' || $updateStatus == 'false') {
                                $newStatus = 2;
                            }
                            if ($newStatus === null && $updateStatus !== '') {
                                Mage::throwException('An invalid value was imported for the product status column. It should contain values like "Enabled" or "Disabled".');
                            }
                            if ($currentStatus != $newStatus) {
                                if ($newStatus === 1) {
                                    Mage::getModel('catalog/product_status')->updateProductStatus($productId, Mage_Core_Model_App::ADMIN_STORE_ID, Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
                                } else {
                                    Mage::getModel('catalog/product_status')->updateProductStatus($productId, Mage_Core_Model_App::ADMIN_STORE_ID, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
                                }
                                $productFieldsUpdated['status'] = $newStatus;
                                array_push($this->_updatedProductStatuses, $productId);
                            }
                        }
                    }
                }
            }

            if (is_array($productFieldsUpdated)) {
                $tempPriceFields = $productFieldsUpdated;
                array_walk($tempPriceFields, create_function('&$i,$k', '$i=" \"$k\"=\"$i\"";'));
                // Add debug messages to $importResult
                if (isset($importResult['error']) || (isset($importResult['changed']) && $importResult['changed'] === false)) {
                    if ($this->getTestMode()) {
                        $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' would have been updated. Identified product ID is %s. Updated fields: %s", $productIdentifier, $productId, implode($tempPriceFields, " ")));
                    } else {
                        $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' has been updated. Identified product ID is %s. Updated fields: %s", $productIdentifier, $productId, implode($tempPriceFields, " ")));
                    }
                } else if (isset($importResult['changed']) && $importResult['changed'] === true) {
                    $importResult['debug'] .= implode($tempPriceFields, "");
                }
            }
        } else {
            // Product not found.
            #$importResult = array('error' => Mage::helper('xtento_stockimport')->__("Product '%s' could not be found in Magento. We tried to identify the product by using the attribute '%s'.", $productIdentifier, $this->_attributeToLoadBy));
            $importResult = array('changed' => false);
        }

        return $importResult;
    }

    /*
     * See which fields changed and if necessary modify other fields based on fields - example qty <= 0 -> is_in_stock = false
     */
    private function _getUpdatedFields($updateData, $productId)
    {
        $updatedFields = array();

        /*if (empty($this->_stockItems)) {
            return $updatedFields;
        }*/

        /*
         * First run: See which values changed, adjust values based on that and return fields to update.
         */
        if (!isset($this->_stockItems[$updateData['stock_id']]) || !isset($this->_stockItems[$updateData['stock_id']][$productId])) {
            // New stock item/status
            foreach ($updateData as $field => $newValue) {
                if ($field == 'price' || $field == 'special_price' || $field == 'cost' || $field == 'stock_id' || $field == 'status') { // Do not process these here.
                    continue;
                }
                if ($field == 'manage_stock' || $field == 'is_in_stock' || $field == 'notify_stock_qty') {
                    $newValue = (int)$newValue; // Should be an integer coming from the database.
                }
                $updatedFields[$field] = $newValue;
            }
        } else {
            // Already existing
            $stockItem = $this->_stockItems[$updateData['stock_id']][$productId];
            foreach ($updateData as $field => $newValue) {
                foreach ($stockItem as $stockField => $stockValue) {
                    if ($stockField == 'price' || $stockField == 'special_price' || $stockField == 'cost' || $stockField == 'stock_id' || $stockField == 'status') { // Do not process these here.
                        continue;
                    }
                    if ($field == $stockField) {
                        // Type casting - everything coming from the database is a string (apparently, at least in my tests)
                        if ($stockField == 'manage_stock' || $stockField == 'is_in_stock' || $stockField == 'notify_stock_qty') {
                            $stockValue = (int)$stockValue; // Should be an integer coming from the database.
                        }
                        // Preparing field values
                        if ($stockField == 'qty') {
                            if ($this->getConfigFlag('import_relative_stock_level')) {
                                // Check for relative updating
                                if ($newValue[0] == '+') {
                                    $newValue = $stockValue + substr($newValue, 1);
                                }
                                if ($newValue[0] == '-') {
                                    $newValue = $stockValue - substr($newValue, 1);
                                }
                            }
                        }
                        // Compare and see if the value changed at all
                        if ($newValue !== $stockValue) {
                            if (trim($newValue) !== '') {
                                $updatedFields[$field] = $newValue;
                            }
                        }
                        // Uncomment this to *increment* stock levels by the imported QTY instead of replacing the stock level with the imported QTY.
                        /*
                        if ($stockField == 'qty') {
                            if ($stockValue <= 0) $stockValue = 0;
                            $updatedFields[$field] = $stockValue + $newValue;
                        }
                        */
                        break 1;
                    }
                }
            }
        }

        /*
         * Second run: See if we have to adjust values based on other field values.
         */
        foreach ($updatedFields as $field => $value) {
            if ($field == 'qty' && !isset($updateData['is_in_stock'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                if (!isset($stockItem) || (int)$stockItem['use_config_min_qty'] === 1) {
                    $outOfStockValue = (int)Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                } else {
                    $outOfStockValue = $stockItem['min_qty'];
                }
                /* $value = Stock level */
                if ($this->getConfigFlag('mark_out_of_stock') && $value <= $outOfStockValue) {
                    $updatedFields['is_in_stock'] = 0;
                } else if ($value > 0) {
                    $updatedFields['is_in_stock'] = (int)($value > $outOfStockValue);
                }
                #var_dump((int)$stockItem['use_config_min_qty'], $outOfStockValue, $updatedFields['is_in_stock']); die();
            }
        }

        return $updatedFields;
    }

    private function _insertStockItem($productId, $productIdentifier, $updateData)
    {
        // Some debugging information
        $tempUpdateData = $updateData;
        array_walk($tempUpdateData, create_function('&$i,$k', '$i=" \"$k\"=\"$i\"";'));
        if ($this->getTestMode()) {
            $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' (New stock item) would have been imported into Magento. Identified product ID is %s. New fields: %s", $productIdentifier, $productId, implode($tempUpdateData, "")));
            return $importResult;
        }

        // Prepare the stock_item and insert it
        if (isset($updateData['qty'])) {
            if (!isset($updateData['is_in_stock'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                $outOfStockValue = (int)Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                if ($this->getConfigFlag('mark_out_of_stock') && $updateData['qty'] <= $outOfStockValue) {
                    $updateData['is_in_stock'] = 0;
                } else if ($updateData['qty'] > 0) {
                    $updateData['is_in_stock'] = (int)($updateData['qty'] > $outOfStockValue);
                }
            }
        }
        #$updateData['stock_id'] = 1;
        $updateData['product_id'] = $productId;
        $this->_writeAdapter->insert($this->getTableName('cataloginventory/stock_item'), $updateData);

        // Insert stock movements into EmbeddedERP stock_movement table
        /*$this->_writeAdapter->insert($this->getTableName('AdvancedStock/StockMovement'), array(
            'sm_product_id' => $productId,
            'sm_qty' => $updateData['qty'],
            'sm_coef' => '0',
            'sm_description' => sprintf('Stock adjustment from StockQty %.4f', $this->_stockItems[$updateData['stock_id']][$productId]['qty']),
            'sm_type' => 'adjustment',
            'sm_date' => date('Y-m-d H:i:s'),
            'sm_source_stock' => '0',
            'sm_target_stock' => $updateData['stock_id']
        ));*/
        // End EmbeddedERP stock_movement code

        // Import result
        $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' (New stock item) has been imported into Magento. Identified product ID is %s. New fields: %s", $productIdentifier, $productId, implode($tempUpdateData, "")));
        return $importResult;
    }

    private function _insertStockStatus($productId, $updatedFields)
    {
        // Entry in stock_status does not exist yet, insert it
        #$updateData['stock_id'] = 1;
        $updateData['product_id'] = $productId;
        if (isset($updatedFields['is_in_stock'])) {
            $updateData['stock_status'] = $updatedFields['is_in_stock'];
        }
        if (isset($updatedFields['qty'])) {
            if (!isset($updateData['stock_status'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                $outOfStockValue = (int)Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                if ($this->getConfigFlag('mark_out_of_stock') && $updatedFields['qty'] <= $outOfStockValue) {
                    $updateData['stock_status'] = 0;
                } else if ($updatedFields['qty'] > 0) {
                    $updateData['stock_status'] = (int)($updatedFields['qty'] > $outOfStockValue);
                }
            }
            $updateData['qty'] = $updatedFields['qty'];
        }
        if (isset($updatedFields['stock_id'])) {
            $updateData['stock_id'] = $updatedFields['stock_id'];
        }

        foreach (Mage::app()->getWebsites() as $website) {
            $updateData['website_id'] = $website->getId();
            $this->_writeAdapter->insert($this->getTableName('cataloginventory/stock_status'), $updateData);
        }

        return $this;
    }

    private function _updateStockItem($productId, $productIdentifier, $updatedFields, $updateData)
    {
        // Some debugging information
        $tempUpdatedFields = $updatedFields;
        array_walk($tempUpdatedFields, create_function('&$i,$k', '$i=" \"$k\"=\"$i\"";'));
        if ($this->getTestMode()) {
            // Don't touch the stock item. Just return some fancy debug information.
            $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' would have been imported into Magento. Identified product ID is %s. Updated fields: %s", $productIdentifier, $productId, implode($tempUpdatedFields, "")));
            return $importResult;
        }

        // Update stock_item
        $this->_writeAdapter->update($this->getTableName('cataloginventory/stock_item'), $updatedFields, "product_id=$productId and stock_id=" . $updateData['stock_id']);

        // Insert stock movements into EmbeddedERP stock_movement table
        /*$this->_writeAdapter->insert($this->getTableName('AdvancedStock/StockMovement'), array(
            'sm_product_id' => $productId,
            'sm_qty' => $updateData['qty'],
            'sm_coef' => '0',
            'sm_description' => sprintf('Stock adjustment from StockQty %.4f', $this->_stockItems[$updateData['stock_id']][$productId]['qty']),
            'sm_type' => 'adjustment',
            'sm_date' => date('Y-m-d H:i:s'),
            'sm_source_stock' => '0',
            'sm_target_stock' => $updateData['stock_id']
        ));*/
        // End EmbeddedERP stock_movement code

        // Import result
        $importResult = array('changed' => true, 'debug' => Mage::helper('xtento_stockimport')->__("Product '%s' has been imported into Magento. Identified product ID is %s. Updated fields: %s", $productIdentifier, $productId, implode($tempUpdatedFields, "")));
        return $importResult;
    }

    private function _updateStockStatus($productId, $updatedFields, $updateData)
    {
        // Entry in stock_status already exists, update it
        $statusUpdate = array();
        if (isset($updatedFields['qty'])) {
            $statusUpdate['qty'] = $updatedFields['qty'];
        }
        if (isset($updatedFields['is_in_stock'])) {
            $statusUpdate['stock_status'] = $updatedFields['is_in_stock'];
        }

        // Update it only if something has changed which is interesting for the stock_status
        if (!empty($statusUpdate)) {
            $this->_writeAdapter->update($this->getTableName('cataloginventory/stock_status'), $statusUpdate, "product_id=$productId and stock_id=" . $updateData['stock_id']); // . " and website_id=1");
        }

        return $this;
    }

    /*
     * After the import ran, currently the only thing done is committing the transaction and reindexing
     */
    public function afterRun()
    {
        // Commit the transaction, only if no product data is updated
        if (!self::$importPrices && !self::$importSpecialPrices && !self::$importCost && !self::$importProductStatus) {
            $this->_writeAdapter->commit();
        }
        #$this->_writeAdapter->query('UNLOCK TABLES');

        // Reindex routine
        if ($this->getTestMode()) {
            $this->getLogEntry()->addDebugMessage('Test mode enabled. Not running any reindex action.');
            return $this;
        }
        try {
            if (!empty($this->_modifiedStockItems)) {
                // Check if BoostMyShop Advanced Stock is installed, and if yes, update stock there:
                if (Mage::helper('xtcore/utils')->isExtensionInstalled('MDN_SalesOrderPlanning')) {
                    foreach ($this->_modifiedStockItems as $productId) {
                        Mage::helper('SalesOrderPlanning/ProductAvailabilityStatus')->RefreshForOneProduct($productId);
                    }
                }
                // Get "configurable products" and if update all associated child products
                /*if (!empty($this->_productTypeMap)) {
                    foreach ($this->_productTypeMap as $parentProductId => $productType) {
                        if ($productType == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                            $parentProduct = Mage::getModel('catalog/product')->load($parentProductId);
                            $parentStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($parentProductId);
                            if ($parentStockItem->getId()) {
                                $childProducts = Mage::getModel('catalog/product_type_configurable')
                                    ->getUsedProducts(null, $parentProduct);
                                foreach ($childProducts as $childProduct) {
                                    $childStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct->getId());
                                    if ($parentStockItem->getQty() !== $childStockItem->getQty()) {
                                        $childStockItem->setQty($parentStockItem->getQty())->save();
                                    }
                                }
                            }
                        }
                    }
                }*/
                // Reindex, if required
                $this->getLogEntry()->addDebugMessage('Starting reindex.');
                if ($this->getConfig('reindex_mode') == 'full' ||
                    (($this->getConfig('reindex_mode') == 'changed' || $this->getConfig('reindex_mode') == 'flag_index') && !Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>='))
                ) {
                    // Full reindex when reindex_mode == full OR (if reindex_mode == changed || reindex mode == flag_index) and Magento version < 1.4
                    $this->getLogEntry()->addDebugMessage('Running full reindex.');
                    $startTime = microtime(true);
                    if (!Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                        if ($this->getConfig('reindex_mode') == 'changed') {
                            $this->getLogEntry()->addDebugMessage('Doing a full reindex even though you selected "changed only reindex" as you are not running Magento version 1.4 or later.');
                        }
                        if ($this->getConfig('reindex_mode') == 'flag_index') {
                            $this->getLogEntry()->addDebugMessage('Doing a full reindex even though you selected "flag index" as you are not running Magento version 1.4 or later.');
                        }
                    }
                    if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                        // 1.4 and newer
                        Mage::getSingleton('index/indexer')
                            ->getProcessByCode('cataloginventory_stock')
                            ->reindexEverything();
                    } else {
                        // 1.3 and below
                        Mage::getSingleton('cataloginventory/stock_status')->rebuild();
                    }
                    $this->getLogEntry()->addDebugMessage(sprintf('Full reindex completed in %d seconds.', (microtime(true) - $startTime)));
                } else if ($this->getConfig('reindex_mode') == 'changed') {
                    // Work in progress. Does not work yet.
                    /*$this->getLogEntry()->addDebugMessage('Running reindex for changed stock items only.');
                    // Reindexing only changed items
                    $productIds = $this->_modifiedStockItems;
                    foreach ($productIds as $productId) {
                        $productAction = Mage::getModel("catalog/product_action");
                        $indexEvent = Mage::getModel("index/event")
                            ->setEntity(age_CatalogInventory_Model_Stock_Item::ENTITY)
                            ->setType(Mage_Index_Model_Event::TYPE_SAVE)
                            ->setDataObject($productAction);
                        $productAction->setWebsiteIds(array(0));
                        $productAction->setProductId($productId);
                        Mage::getSingleton("index/indexer")->getProcessByCode('cataloginventory_stock')->register($indexEvent)->processEvent($indexEvent);
                    } // OR:
                    $productAction = Mage::getModel("catalog/product_action");
                    $indexEvent = Mage::getModel("index/event")
                        ->setEntity(Mage_Catalog_Model_Product::ENTITY)
                        ->setType(Mage_Index_Model_Event::TYPE_MASS_ACTION)
                        ->setDataObject($productAction);
                    $productAction->setWebsiteIds(array(0));
                    $productAction->setProductIds($productIds);
                    Mage::getSingleton("index/indexer")->getProcessByCode('cataloginventory_stock')->register($indexEvent)->processEvent($indexEvent);*/
                } else if ($this->getConfig('reindex_mode') == 'flag_index') {
                    $this->getLogEntry()->addDebugMessage('Flagging stock item index as reindex required.');
                    // Flag as reindex required
                    Mage::getSingleton('index/indexer')
                        ->getProcessByCode('cataloginventory_stock')
                        ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                } else if ($this->getConfig('reindex_mode') == 'no_reindex') {
                    $this->getLogEntry()->addDebugMessage('Reindexing disabled. Not touching index at all.');
                }
            } else {
                $this->getLogEntry()->addDebugMessage('No stock items modified. No reindex actions required.');
            }

            // Refresh Magento Enterprise Edition Full Page Cache
            if ($this->getConfig('enterprise_fpc_action') == 'invalidate') {
                $this->getLogEntry()->addDebugMessage('Invalidating Magento Enterprise Full Page Cache.');
                Mage::app()->getCacheInstance()->invalidateType('full_page');
            } else if ($this->getConfig('enterprise_fpc_action') == 'clean') {
                $this->getLogEntry()->addDebugMessage('Cleaning Magento Enterprise Full Page Cache.');
                Mage::app()->getCacheInstance()->cleanType('full_page');
                Enterprise_PageCache_Model_Cache::getCacheInstance()->flush();
            }

            if ($this->getConfigFlag('update_low_stock_date')) {
                // Refresh "Low stock date"
                Mage::getResourceSingleton('cataloginventory/stock')->updateLowStockDate();
            }

            // Reindex for price updates
            if (self::$importPrices || self::$importSpecialPrices) {
                if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '>=')) {
                    if ($this->getConfig('reindex_mode') == 'full') {
                        $this->getLogEntry()->addDebugMessage('Price update: Running full reindex.');
                        $startTime = microtime(true);
                    }
                    $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
                    foreach ($processes as $indexerProcess) {
                        $indexerProcess = Mage::getModel('index/process')->load($indexerProcess->getProcessId());
                        if (!in_array($indexerProcess->getIndexerCode(), array('catalog_product_price', 'catalog_product_flat', 'catalog_category_product'))) {
                            continue;
                        }
                        $indexerProcess->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
                        if (!empty($this->_updatedPrices) || !empty($this->_updatedSpecialPrices) || !empty($this->_updatedProductStatuses)) {
                            if ($this->getConfig('reindex_mode') == 'full') {
                                $indexerProcess->reindexAll();
                                #$indexerProcess->reindexEverything();
                            }
                        }
                    }
                    if ($this->getConfig('reindex_mode') == 'full') {
                        $this->getLogEntry()->addDebugMessage(sprintf('Price update: Full reindex completed in %d seconds.', (microtime(true) - $startTime)));
                    }
                }
            }
        } catch (Exception $e) {
            $this->getLogEntry()->addDebugMessage('ERROR while reindexing. Exception: ' . $e->getMessage());
        }
        // End of reindexing routine
        $this->getLogEntry()->addDebugMessage('Done: afterRun() (Reindexer functions, ...)');
    }
}