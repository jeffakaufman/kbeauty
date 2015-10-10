<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-03-17T15:33:51+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Grid/Renderer/Configuration.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Grid_Renderer_Configuration extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $configuration = array();
        if ($row->getType() == Xtento_ProductExport_Model_Destination::TYPE_LOCAL) {
            $configuration['directory'] = $row->getPath();
        }
        if ($row->getType() == Xtento_ProductExport_Model_Destination::TYPE_FTP || $row->getType() == Xtento_ProductExport_Model_Destination::TYPE_SFTP) {
            $configuration['server'] = $row->getHostname().':'.$row->getPort();
            $configuration['username'] = $row->getUsername();
            $configuration['path'] = $row->getPath();
        }
        if ($row->getType() == Xtento_ProductExport_Model_Destination::TYPE_EMAIL) {
            $configuration['from'] = $row->getEmailSender();
            $configuration['to'] = $row->getEmailRecipient();
            $configuration['subject'] = $row->getEmailSubject();
        }
        if ($row->getType() == Xtento_ProductExport_Model_Destination::TYPE_CUSTOM) {
            $configuration['class'] = $row->getCustomClass();
        }
        if ($row->getType() == Xtento_ProductExport_Model_Destination::TYPE_WEBSERVICE) {
            $configuration['class'] = 'Webservice';
            $configuration['function'] = $row->getCustomFunction();
        }
        if (!empty($configuration)) {
            $configurationHtml = '';
            foreach ($configuration as $key => $value) {
                $configurationHtml .= Mage::helper('xtento_productexport')->__(ucfirst($key)).': <i>'.Mage::helper('xtcore/core')->escapeHtml($value).'</i><br/>';
            }
            return $configurationHtml;
        } else {
            return '---';
        }
    }
}