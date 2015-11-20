<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2015-03-04T15:57:34+01:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Log/Grid/Renderer/Source.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Log_Grid_Renderer_Source extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    static $sources = array();

    public function render(Varien_Object $row)
    {
        $sourceIds = $row->getSourceIds();
        $sourceText = "";
        if (empty($sourceIds)) {
            return Mage::helper('xtento_stockimport')->__('No source selected.');
        }
        foreach (explode("&", $sourceIds) as $sourceId) {
            if (!empty($sourceId) && is_numeric($sourceId)) {
                if (!isset(self::$sources[$sourceId])) {
                    $source = Mage::getModel('xtento_stockimport/source')->load($sourceId);
                    self::$sources[$sourceId] = $source;
                } else {
                    $source = self::$sources[$sourceId];
                }
                if ($source->getId()) {
                    $sourceText .= $source->getName() . " (".Mage::getSingleton('xtento_stockimport/system_config_source_source_type')->getName($source->getType()).")<br>";
                }
            }
        }
        return $sourceText;
    }
}