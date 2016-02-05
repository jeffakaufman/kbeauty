<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-09-23T16:45:02+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Source/Grid/Renderer/Configuration.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Source_Grid_Renderer_Configuration extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $configuration = array();
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_LOCAL) {
            $configuration['directory'] = $row->getPath();
        }
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_FTP || $row->getType() == Xtento_StockImport_Model_Source::TYPE_SFTP) {
            $configuration['server'] = $row->getHostname() . ':' . $row->getPort();
            $configuration['username'] = $row->getUsername();
            $configuration['path'] = $row->getPath();
        }
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_CUSTOM) {
            $configuration['class'] = $row->getCustomClass();
        }
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_HTTPDOWNLOAD) {
            $configuration['Link'] = $row->getCustomFunction();
        }
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_WEBSERVICE) {
            $configuration['class'] = 'Webservice';
            $configuration['function'] = $row->getCustomFunction();
        }
        if ($row->getType() == Xtento_StockImport_Model_Source::TYPE_LOCAL || $row->getType() == Xtento_StockImport_Model_Source::TYPE_FTP || $row->getType() == Xtento_StockImport_Model_Source::TYPE_SFTP) {
            if ($row->getFilenamePattern() !== '') {
                $configuration['filename pattern'] = $row->getFilenamePattern();
            }
            if ($row->getArchivePath() !== '') {
                $configuration['archive path'] = $row->getArchivePath();
            }
            if ($row->getDeleteImportedFiles() !== '') {
                if ($row->getDeleteImportedFiles()) {
                    $configuration['delete files'] = Mage::helper('xtento_stockimport')->__('Yes');
                } else {
                    $configuration['delete files'] = Mage::helper('xtento_stockimport')->__('No');
                }
            }
        }
        if (!empty($configuration)) {
            $configurationHtml = '';
            foreach ($configuration as $key => $value) {
                $configurationHtml .= Mage::helper('xtento_stockimport')->__(ucwords($key)) . ': <i>' . Mage::helper('xtcore/core')->escapeHtml($value) . '</i><br/>';
            }
            return $configurationHtml;
        } else {
            return '---';
        }
    }
}