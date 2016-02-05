<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-05-06T22:26:51+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Output/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_ProductExport_Model_Output_Abstract extends Mage_Core_Model_Abstract implements Xtento_ProductExport_Model_Output_Interface
{
    static $iteratingKeys = array('items', 'custom_options', 'product_attributes', 'product_options');

    protected function _replaceFilenameVariables($filename, $exportArray)
    {
        $filename = str_replace("|", "-", $filename); // Remove the pipe character - it's not allowed in file names anyways and we use it to separate multiple files in the DB
        // Replace variables in filename
        $replaceableVariables = array(
            '/%d%/' => Mage::getSingleton('core/date')->date('d'),
            '/%m%/' => Mage::getSingleton('core/date')->date('m'),
            '/%y%/' => Mage::getSingleton('core/date')->date('y'),
            '/%Y%/' => Mage::getSingleton('core/date')->date('Y'),
            '/%h%/' => Mage::getSingleton('core/date')->date('H'),
            '/%i%/' => Mage::getSingleton('core/date')->date('i'),
            '/%s%/' => Mage::getSingleton('core/date')->date('s'),
            '/%timestamp%/' => Mage::getSingleton('core/date')->timestamp(time()),
            '/%lastentityid%/' => $this->getVariableValue('last_entity_id', $exportArray),
            '/%collectioncount%/' => $this->getVariableValue('collection_count', $exportArray),
            '/%uuid%/' => uniqid(),
            '/%exportid%/' => $this->getVariableValue('export_id', $exportArray),
        );
        $filename = preg_replace(array_keys($replaceableVariables), array_values($replaceableVariables), $filename);
        return $filename;
    }

    protected function getVariableValue($variable, $exportArray)
    {
        $arrayToWorkWith = $exportArray;
        if ($variable == 'export_id') {
            if (Mage::registry('product_export_log')) {
                return Mage::registry('product_export_log')->getId();
            } else {
                return 0;
            }
        }
        if ($variable == 'collection_count') {
            return count($arrayToWorkWith);
        }
        if ($variable == 'total_item_count') {
            $totalItemCount = 0;
            foreach ($arrayToWorkWith as $collectionObject) {
                if (isset($collectionObject['items'])) {
                    foreach ($collectionObject['items'] as $item) {
                        $totalItemCount++;
                    }
                }
            }
            return $totalItemCount;
        }
        if ($variable == 'last_entity_id') {
            $lastItem = array_pop($arrayToWorkWith);
            if (isset($lastItem['entity_id'])) {
                return $lastItem['entity_id'];
            }
        }
        if ($variable == 'date_from_timestamp') {
            $firstObject = array_shift($arrayToWorkWith);
            return Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($firstObject['created_at']);
        }
        if ($variable == 'date_to_timestamp') {
            $lastObject = array_pop($arrayToWorkWith);
            return Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($lastObject['created_at']);
        }
        if ($variable == 'root_category_id') {
            $firstObject = array_shift($arrayToWorkWith);
            $storeId = 0;
            if (isset($firstObject['store_id'])) {
                $storeId = $firstObject['store_id'];
            }
            return Mage::app()->getStore($storeId)->getRootCategoryId();
        }
        return '';
    }

    protected function _throwXmlException($message)
    {
        $message .= "\n";
        foreach (libxml_get_errors() as $error) {
            $message .= "\tLine " . $error->line . ": " . $error->message;
            if (strpos($error->message, "\n") === FALSE) {
                $message .= "\n";
            }
        }
        libxml_clear_errors();
        Mage::throwException($message);
    }

    protected function _changeEncoding($input, $encoding, $charsetLocale = '')
    {
        if (!empty($charsetLocale)) {
            // Set locale based on XSL Template "locale" attribute
            $oldLocale = setlocale(LC_CTYPE, "0"); // Get current locale
            @setlocale(LC_CTYPE, $charsetLocale);
        }
        $output = $input;
        if (!empty($encoding) && @function_exists('iconv')) {
            $output = @iconv("UTF-8", $encoding, $input);
            if (!$output && !empty($input)) {
                // Conversion failed, try as UTF-8 re-encoded
                $output = @iconv("UTF-8", $encoding, utf8_encode(utf8_decode($input)));
                if (!$output && !empty($input)) {
                    if (!empty($charsetLocale)) {
                        // Reset locale
                        setlocale(LC_CTYPE, $oldLocale);
                    }
                    $this->_throwXmlException(Mage::helper('xtento_productexport')->__("While trying to convert your export data into the requested encoding '%s', the PHP iconv() function failed. You either forgot to add //IGNORE to the encoding, or you are affected by this bug: https://bugs.php.net/bug.php?id=48147", $encoding));
                }
            }
        }
        if (!empty($charsetLocale)) {
            // Reset locale
            setlocale(LC_CTYPE, $oldLocale);
        }
        return $output;
    }
}