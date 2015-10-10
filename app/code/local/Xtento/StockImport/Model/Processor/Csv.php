<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2015-01-19T17:52:56+01:00
 * File:          app/code/local/Xtento/StockImport/Model/Processor/Csv.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Processor_Csv extends Xtento_StockImport_Model_Processor_Abstract
{
    protected $config = array();
    protected $headerRow;

    private function _initConfiguration()
    {
        if (!$this->config) {
            # Load configuration:
            $this->config = array(
                'IMPORT_SKIP_HEADER' => $this->getConfigValue('skip_header'),
                'IMPORT_DELIMITER' => $this->getConfigValue('delimiter'),
                'IMPORT_ENCLOSURE' => $this->getConfigValue('enclosure'),
            );

            # Get mapping model
            $this->mappingModel = Mage::getModel('xtento_stockimport/processor_mapping_fields');
            $this->mappingModel->setMappingData($this->getConfigValue('mapping'));

            # Load mapping
            $this->mapping = $this->mappingModel->getMappingConfig();

            if ($this->mapping->getProductIdentifier() == null) {
                Mage::throwException('Please configure the CSV processor in the configuration section of this import profile. The Product Identifier field may not be empty and must be mapped.');
            }
            if ($this->config['IMPORT_DELIMITER'] == '') {
                Mage::throwException('Please configure the CSV processor in the configuration section of this import profile. The Field Delimiter may not be empty.');
            }
            if ($this->config['IMPORT_ENCLOSURE'] == '') {
                $this->config['IMPORT_ENCLOSURE'] = '"';
            }
            if (strtolower($this->config['IMPORT_DELIMITER']) == 'tab' || $this->config['IMPORT_DELIMITER'] == '\t' || $this->config['IMPORT_DELIMITER'] == chr(9)) {
                $this->config['IMPORT_DELIMITER'] = "\t";
            }
            if (strtolower($this->config['IMPORT_DELIMITER']) == 'flf') {
                $this->config['IMPORT_FIXED_LENGTH_FORMAT'] = true;
            } else {
                $this->config['IMPORT_FIXED_LENGTH_FORMAT'] = false;
            }
        }
    }

    public function getRowsToProcess($filesToProcess)
    {
        @ini_set('auto_detect_line_endings', 1);

        # Logger for this processor
        //$writer = new Zend_Log_Writer_Stream(Mage::getBaseDir('log') . DS . 'inventory_import_processor_csv.log');
        //$log = new Zend_Log($writer);

        # Updates to process, later the result
        $updatesInFilesToProcess = array();

        $this->_initConfiguration();

        // Add fake updates
        /* TEST CODE
        $csvFile = '';
        $instockSku = array();

        foreach (explode("\n", $file) as $line) {
            $csvFile .= trim($line) . ",0\r\n";
            $instockSku[] = $line;
        }

        $coll = Mage::getModel('catalog/product')->getCollection();

        foreach ($coll as $product) {
            $sku = $product->getSku();
            if (!isset($instockSku[$sku])) $csvFile .= $sku . ",5000\r\n";
        }

        file_put_contents($config->getData('base_path') . uniqid() . '.csv', $csvFile);
        */

        foreach ($filesToProcess as $importFile) {
            $data = $importFile['data'];
            $filename = $importFile['filename'];
            unset($importFile['data']);

            // Remove UTF8 BOM
            $bom = pack('H*', 'EFBBBF');
            $data = preg_replace("/^$bom/", '', $data);

            $updatesToProcess = array();
            $foundFields = array();
            $rowCounter = 0;

            if ($this->config['IMPORT_FIXED_LENGTH_FORMAT']) {
                // Fixed length format
                foreach (explode("\n", $data) as $line) {
                    $rowCounter++;
                    if ($rowCounter == 1) {
                        // Skip the header
                        if ($this->config['IMPORT_SKIP_HEADER'] == true) {
                            continue;
                        }
                    }

                    $productIdentifierPosition = explode("-", $this->getFieldPos('product_identifier'));
                    if (!isset($productIdentifierPosition[1])) continue;
                    $productIdentifier = trim(substr($line, $productIdentifierPosition[0] - 1, $productIdentifierPosition[1] - $productIdentifierPosition[0]));
                    if (empty($productIdentifier)) {
                        continue;
                    }

                    $stockIdPosition = explode("-", $this->getFieldPos('stock_id'));
                    if (!isset($stockIdPosition[1])) {
                        $stockId = trim(substr($line, $stockIdPosition[0] - 1, $stockIdPosition[1] - $stockIdPosition[0]));
                    } else {
                        $stockId = false;
                    }
                    if (empty($stockId)) {
                        $stockId = 1;
                    }

                    if (!isset($updatesToProcess[$stockId][$productIdentifier])) {
                        foreach ($this->mappingModel->getMappingFields() as $fieldName => $mappingField) {
                            if (isset($mappingField['disabled']) && $mappingField['disabled']) {
                                continue;
                            }
                            $fieldPosition = explode("-", $this->getFieldPos($fieldName));
                            if (!isset($fieldPosition[1])) continue;
                            $fieldValue = trim(substr($line, $fieldPosition[0] - 1, $fieldPosition[1] - $fieldPosition[0]));
                            if ($fieldValue !== '') {
                                if (!in_array($fieldName, $foundFields)) {
                                    $foundFields[] = $fieldName;
                                }
                                $updatesToProcess[$stockId][$productIdentifier][$fieldName] = $this->mappingModel->formatField($fieldName, $fieldValue);
                            }
                        }
                    } else {
                        // Sum up multiple warehouses qtys for example to $updates
                        $fieldPosition = explode("-", $this->getFieldPos('qty'));
                        if (!isset($fieldPosition[1])) continue;
                        $fieldValue = trim(substr($line, $fieldPosition[0] - 1, $fieldPosition[1] - $fieldPosition[0]));
                        if ($fieldValue !== '') {
                            $oldQty = $updatesToProcess[$stockId][$productIdentifier]['qty'];
                            if ($oldQty <= 0) $oldQty = 0;
                            $updatesToProcess[$stockId][$productIdentifier]['qty'] = $oldQty + $this->mappingModel->formatField('qty', $fieldValue);
                            // Check relative update and prepend + if required
                            if ($oldQty[0] == '+' || $oldQty[0] == '-') {
                                if ($updatesToProcess[$stockId][$productIdentifier]['qty'] > 0) {
                                    $updatesToProcess[$stockId][$productIdentifier]['qty'] = '+' . $updatesToProcess[$stockId][$productIdentifier]['qty'];
                                }
                            }
                        }
                    }
                }
            } else {
                // Traditional CSV format
                $fileHandle = fopen('php://memory', 'rw');
                fwrite($fileHandle, $data);
                rewind($fileHandle);

                // Sample code for file formats like this with columns for sizes: STYLE#,COLOR,OO,O,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,List Price
                // Rewrite CSV file with faked SKUs
                /*
                $this->headerRow = array();
                $newFile = "SKU,Qty,Price\r\n";
                while (($rowData = fgetcsv($fileHandle, 0, $this->config['IMPORT_DELIMITER'], $this->config['IMPORT_ENCLOSURE'])) !== false) {
                    $rowCounter++;
                    if ($rowCounter == 1) {
                        // Skip the header line but parse it for field names.
                        $numberOfFields = count($rowData);
                        for ($i = 0; $i < $numberOfFields; $i++) {
                            $this->headerRow[$i] = $rowData[$i];
                        }
                        continue;
                    }
                    $style = $rowData[0];
                    $color = $rowData[1];
                    $price = $rowData[20];
                    for ($i = 2; $i < 20; $i++) {
                        $size = $this->headerRow[$i];
                        $newSku = $style . "-" . $color . "-" . $size;
                        $qty = $rowData[$i];
                        $newFile .= $newSku . "," . $qty . "," . $price . "\r\n";
                    }
                }
                #echo $newFile;
                #die();
                // Write "new faked file"
                rewind($fileHandle);
                fwrite($fileHandle, $newFile);
                rewind($fileHandle);
                // Reset rowCounter
                $rowCounter = 0;
                */
                // End sample code

                $this->headerRow = array();

                while (($rowData = fgetcsv($fileHandle, 0, $this->config['IMPORT_DELIMITER'], $this->config['IMPORT_ENCLOSURE'])) !== false) {
                    $this->rowData = $rowData;

                    $rowCounter++;
                    if ($rowCounter == 1) {
                        // Skip the header line but parse it for field names.
                        $numberOfFields = count($rowData);
                        for ($i = 0; $i < $numberOfFields; $i++) {
                            $this->headerRow[$rowData[$i]] = $i;
                        }
                        if ($this->config['IMPORT_SKIP_HEADER'] == true) {
                            continue;
                        }
                    }

                    $productIdentifier = $this->getFieldData('product_identifier');
                    if (empty($productIdentifier)) {
                        continue;
                    }

                    $stockId = $this->getFieldData('stock_id');
                    if (empty($stockId)) {
                        $stockId = 1;
                    }

                    if (!isset($updatesToProcess[$stockId][$productIdentifier])) {
                        foreach ($this->mappingModel->getMappingFields() as $fieldName => $mappingField) {
                            if (isset($mappingField['disabled']) && $mappingField['disabled']) {
                                continue;
                            }
                            $fieldValue = $this->getFieldData($fieldName);
                            if ($fieldValue !== '') {
                                if (!in_array($fieldName, $foundFields)) {
                                    $foundFields[] = $fieldName;
                                }
                                $updatesToProcess[$stockId][$productIdentifier][$fieldName] = $this->mappingModel->formatField($fieldName, $fieldValue);
                            }
                        }
                    } else {
                        // Sum up multiple warehouses qtys for example to $updates
                        $fieldValue = $this->getFieldData('qty');
                        if ($fieldValue !== '') {
                            $oldQty = $updatesToProcess[$stockId][$productIdentifier]['qty'];
                            if ($oldQty <= 0) $oldQty = 0;
                            $updatesToProcess[$stockId][$productIdentifier]['qty'] = $oldQty + $this->mappingModel->formatField('qty', $fieldValue);
                        }
                    }
                }
            }

            // Output the header row in a nicer string
            $hasHeaderRow = ($this->config['IMPORT_SKIP_HEADER']) ? "Yes" : "No";
            $headerRowTemp = $this->headerRow;
            array_walk($headerRowTemp, create_function('&$i,$k', '$i=" \"$k\"=\"$i\"";'));
            // File processed
            $updatesInFilesToProcess[] = array(
                "FILE_INFORMATION" => $importFile,
                "HEADER_ROW" => "Skip header row: " . $hasHeaderRow . " | Header row:" . implode($headerRowTemp, ""),
                "FIELDS" => $foundFields,
                "ITEMS" => $updatesToProcess
            );
        }

        @ini_set('auto_detect_line_endings', 0);

        //ini_set('xdebug.var_display_max_depth', 10);
        //Zend_Debug::dump($updatesToProcess);
        //die();

        return $updatesInFilesToProcess;
    }

    public function getFieldPos($field)
    {
        if (!is_numeric($this->mapping->getData($field)) && isset($this->headerRow[$this->mapping->getData($field)])) {
            if ($this->mapping->getData($field) !== null) {
                return $this->headerRow[$this->mapping->getData($field)];
            } else {
                return -1;
            }
        } else {
            return $this->mapping->getData($field);
        }
    }

    public function getFieldData($field)
    {
        if ($this->getFieldPos($field) >= 0 && isset($this->rowData[$this->getFieldPos($field)])) {
            $data = $this->rowData[$this->getFieldPos($field)];
            if ($data == '') {
                // Try to get the default value at least.. otherwise ''
                $data = $this->mappingModel->getDefaultValue($field);
            }
        } else {
            // Try to get the default value at least.. otherwise ''
            $data = $this->mappingModel->getDefaultValue($field);
        }
        return trim($data);
    }
}
