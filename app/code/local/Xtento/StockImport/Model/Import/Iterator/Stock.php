<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-09-04T18:30:00+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Import/Iterator/Stock.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Import_Iterator_Stock extends Xtento_StockImport_Model_Import_Iterator_Abstract
{
    public function processUpdates($updatesInFilesToProcess)
    {
        $logEntry = Mage::registry('stock_import_log');

        $totalRecordCount = 0;
        $updatedRecordCount = 0;

        $importModel = Mage::getModel('xtento_stockimport/import_entity_' . $this->getProfile()->getEntity());
        $importModel->setImportType($this->getImportType());
        $importModel->setTestMode($this->getTestMode());
        $importModel->setProfile($this->getProfile());

        if (!$importModel->prepareImport($updatesInFilesToProcess)) {
            $logEntry->setResult(Xtento_StockImport_Model_Log::RESULT_WARNING);
            $logEntry->addResultMessage(Mage::helper('xtento_stockimport')->__("Files have been parsed, however, the file mapping you set up did not return any data. Please double check your file mapping in the import profile and make sure your import processor is set up right. Stopping import. "));
            return false; // No updates to import.
        }

        foreach ($updatesInFilesToProcess as $updateFile) {
            $path = (isset($updateFile['FILE_INFORMATION']['path'])) ? $updateFile['FILE_INFORMATION']['path'] : '';
            $filename = $updateFile['FILE_INFORMATION']['filename'];
            $sourceId = $updateFile['FILE_INFORMATION']['source_id'];

            $updatesInStockIds = $updateFile['ITEMS'];

            foreach ($updatesInStockIds as $stockId => $updatesToProcess) {
                foreach ($updatesToProcess as $productIdentifier => $updateData) {
                    $totalRecordCount++;
                    try {
                        if (empty($productIdentifier)) {
                            continue;
                        }

                        $updateResult = $importModel->processItem($productIdentifier, $updateData);

                        if (!$updateResult || isset($updateResult['error'])) {
                            $logEntry->addDebugMessage(sprintf("Notice: %s | File '" . $path . $filename . "'", $updateResult['error']));
                            continue;
                        } else {
                            if (isset($updateResult['changed']) && $updateResult['changed']) {
                                $updatedRecordCount++;
                            }
                            if (isset($updateResult['debug'])) {
                                $logEntry->addDebugMessage(sprintf("%s", $updateResult['debug'])); // | File '" . $path . $filename . "'", $updateResult['debug']));
                            }
                        }
                    } catch (Mage_Core_Exception $e) {
                        // Don't break execution, but log the error.
                        $logEntry->addDebugMessage("Exception catched for item with product identifier '" . $productIdentifier . "' specified in '" . $path . $filename . "' from source ID '" . $sourceId . "':\n" . $e->getMessage());
                        continue;
                    }
                }
            }
        }

        $importModel->afterRun();

        $importResult = array('total_record_count' => $totalRecordCount, 'updated_record_count' => $updatedRecordCount);
        return $importResult;
    }
}