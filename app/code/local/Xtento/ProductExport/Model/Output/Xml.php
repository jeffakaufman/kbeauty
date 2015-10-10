<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-02-28T16:34:28+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Output/Xml.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Output_Xml extends Xtento_ProductExport_Model_Output_Abstract
{
    public function convertData($exportArray)
    {
        if (!@class_exists('XMLWriter')) {
            Mage::throwException(Mage::helper('xtento_productexport')->__('The XMLWriter class could not be found. This means your PHP installation is missing XMLWriter features. You cannot export XML/XSL types without XMLWriter. Please get in touch with your hoster or server administrator to add XMLWriter features.'));
        }
        // Some libxml settings
        $useInternalXmlErrors = libxml_use_internal_errors(true);
        if (function_exists('libxml_disable_entity_loader')) {
            #$loadXmlEntities = libxml_disable_entity_loader(true);
        }
        libxml_clear_errors();

        #ini_set('xdebug.var_display_max_depth', 5);
        #Zend_Debug::dump($exportArray); die();
        $profile = $this->getProfile();
        if ($profile->getOutputType() == 'xml') {
            $escapeSpecialChars = true;
            $disableEscapingFields = array();
        } else {
            $escapeSpecialChars = $this->getEscapeSpecialChars();
            $disableEscapingFields = $this->getDisableEscapingFields();
        }
        $xmlWriter = Mage::getModel('xtento_productexport/output_xml_writer');
        $xmlWriter->setEscapeSpecialChars($escapeSpecialChars);
        $xmlWriter->setDisableEscapingFields($disableEscapingFields);
        $xmlWriter->fromArray($exportArray);
        $outputXml = $xmlWriter->getDocument();
        if (libxml_get_last_error() !== FALSE) {
            $this->_throwXmlException(Mage::helper('xtento_productexport')->__("Something is wrong with the internally processed XML markup. Please contact XTENTO."));
        }
        // Handle output if the profiles output format is directly the master XML format
        if ($profile->getOutputType() == 'xml') {
            // Output all fields into a XML file
            $filename = $this->_replaceFilenameVariables($profile->getFilename(), $exportArray);
            $charsetEncoding = $profile->getEncoding();
            $outputXml = $this->_changeEncoding($outputXml, $charsetEncoding);
            $outputData[$filename] = $outputXml;
        } else {
            // We use the output for the XSL Template
            $outputData[] = $outputXml;
        }

        // Reset libxml settings
        libxml_use_internal_errors($useInternalXmlErrors);
        if (function_exists('libxml_disable_entity_loader')) {
            #libxml_disable_entity_loader($loadXmlEntities);
        }
        // Return data
        return $outputData;
    }
}