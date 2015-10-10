<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-09-05T11:12:34+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Profile.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Profile extends Mage_Core_Model_Abstract
{
    /*
     * Profile model containing information about import profiles
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('xtento_stockimport/profile');
    }

    public function getSources()
    {
        $logEntry = Mage::registry('stock_import_log');
        $sourceIds = array_filter(explode("&", $this->getData('source_ids')));
        $sources = array();
        foreach ($sourceIds as $sourceId) {
            if (!is_numeric($sourceId)) {
                continue;
            }
            $source = Mage::getModel('xtento_stockimport/source')->load($sourceId);
            if ($source->getId()) {
                $sources[] = $source;
            } else {
                #if ($logEntry) {
                #    $logEntry->setResult(Xtento_StockImport_Model_Log::RESULT_WARNING);
                #    $logEntry->addResultMessage(Mage::helper('xtento_stockimport')->__('Source ID "%s" could not be found.', $sourceId));
                #}
            }
        }
        // Return sources
        return $sources;
    }
}