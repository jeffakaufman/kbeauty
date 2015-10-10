<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-05-30T18:16:13+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Processor/Xml.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Processor_Xml extends Xtento_StockImport_Model_Processor_Abstract
{
    public function getRowsToProcess($filesToProcess)
    {
        # Get some more detailed error information from libxml
        libxml_use_internal_errors(true);

        $log = null;
        try {
            # Logger for this processor
            $writer = new Zend_Log_Writer_Stream(Mage::getBaseDir('log') . DS . 'stock_import_processor_xml.log');
            $log = new Zend_Log($writer);
        } catch (Exception $e) {
            # Do nothing.. :|
        }

        # Updates to process, later the result
        $updatesInFilesToProcess = array();

        # Get mapping model
        $this->mappingModel = Mage::getModel('xtento_stockimport/processor_mapping_fields');
        $this->mappingModel->setMappingData($this->getConfigValue('mapping'));

        # Load mapping
        $this->mapping = $this->mappingModel->getMappingConfig();

        # Load configuration:
        $config = array(
            'IMPORT_DATA_XPATH' => $this->getConfigValue('xpath_data'),
        );

        if ($this->mapping->getProductIdentifier() == null) {
            Mage::throwException('Please configure the XML processor in the configuration section of this import profile. The product identifier field may not be empty and must be mapped.');
        }
        if ($config['IMPORT_DATA_XPATH'] == '') {
            Mage::throwException('Please configure the XML Processor in the configuration section of this import profile. The Data XPath field may not be empty.');
        }

        foreach ($filesToProcess as $importFile) {
            $data = $importFile['data'];
            $filename = $importFile['filename'];
            unset($importFile['data']);

            // Remove UTF8 BOM
            $bom = pack('H*', 'EFBBBF');
            $data = preg_replace("/^$bom/", '', $data);

            $updatesToProcess = array();
            $foundFields = array();

            // Prepare data - replace namespace
            $data = str_replace('xmlns=', 'ns=', $data); // http://www.php.net/manual/en/simplexmlelement.xpath.php#96153
            $data = str_replace('xmlns:', 'ns:', $data); // http://www.php.net/manual/en/simplexmlelement.xpath.php#96153

            $loadEntities = libxml_disable_entity_loader(true);
            try {
                $xmlObject = new SimpleXMLElement($data);
                libxml_disable_entity_loader($loadEntities);
            } catch (Exception $e) {
                libxml_disable_entity_loader($loadEntities);
                $errors = "Could not load XML File '" . $filename . "':\n";
                foreach (libxml_get_errors() as $error) {
                    $errors .= "\t" . $error->message;
                }
                if ($log instanceof Zend_Log) $log->info($errors);
                continue; # Process next file..
            }

            if (!$xmlObject) {
                $errors = "Could not load XML File '" . $filename . "':\n";
                foreach (libxml_get_errors() as $error) {
                    $errors .= "\t" . $error->message;
                }
                if ($log instanceof Zend_Log) $log->info($errors);
                continue; # Process next file..
            }

            $updates = $xmlObject->xpath($config['IMPORT_DATA_XPATH']);
            foreach ($updates as $update) {
                $this->update = $update;

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

            // File processed
            $updatesInFilesToProcess[] = array(
                "FILE_INFORMATION" => $importFile,
                "FIELDS" => $foundFields,
                "ITEMS" => $updatesToProcess
            );
        }

        //ini_set('xdebug.var_display_max_depth', 10);
        //Zend_Debug::dump($updatesToProcess);
        //die();

        return $updatesInFilesToProcess;
    }

    private function _runCurrentUntilString($array)
    {
        // Run the current function on the returned SimpleXMLElement until a string (just no array!) gets returned
        $runCount = 0;
        while (true) {
            if (is_object($array) && $array instanceof SimpleXMLElement) {
                $tempVal = (string)$array;
                if (!empty($tempVal)) {
                    return $tempVal;
                }
            }
            if (is_array($array) || is_object($array)) {
                $array = current($array);
            } else {
                return $array;
            }
            $runCount++;
            if ($runCount > 15) {
                // Do not run this loop too often.
                return '';
            }
        }
    }

    public function getFieldData($field, $type = 'update')
    {
        if ($this->mapping->getData($field) !== null) {
            $data = $this->_runCurrentUntilString($this->$type->xpath($this->mapping->getData($field)));
            /*
             * Alternate method to pull fields, when xpath fails.
             */
            if ($data == '') {
                foreach ($this->$type as $key => $value) {
                    if ($key == $this->mapping->getData($field)) {
                        $data = (string)$value;
                    }
                }
            }
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
