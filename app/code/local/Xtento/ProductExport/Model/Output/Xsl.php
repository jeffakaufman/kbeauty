<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-05-20T13:31:39+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Output/Xsl.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Output_Xsl extends Xtento_ProductExport_Model_Output_Abstract
{
    protected $_searchCharacters;
    protected $_replaceCharacters;

    public function convertData($exportArray)
    {
        if (!@class_exists('XSLTProcessor')) {
            Mage::throwException(Mage::helper('xtento_productexport')->__('The XSLTProcessor class could not be found. This means your PHP installation is missing XSL features. You cannot export output formats using XSL Templates without the PHP XSL extension. Please get in touch with your hoster or server administrator to add XSL to your PHP configuration.'));
        }
        // Some libxml settings, constants
        $libxmlConstants = null;
        if (defined('LIBXML_PARSEHUGE')) {
            $libxmlConstants = LIBXML_PARSEHUGE;
        }
        $useInternalXmlErrors = libxml_use_internal_errors(true);
        if (function_exists('libxml_disable_entity_loader')) {
            #$loadXmlEntities = libxml_disable_entity_loader(true);
        }
        libxml_clear_errors();

        $outputArray = array();
        // Should the ampersand character etc. be encoded?
        $escapeSpecialChars = false;
        if (preg_match('/method="(xml|html)"/', $this->getProfile()->getXslTemplate())) {
            $escapeSpecialChars = true;
        }
        // Get fields which should not be escaped
        $disableEscapingFields = array();
        if (preg_match_all('/disable-escaping-fields="(.*)"/', $this->getProfile()->getXslTemplate(), $disableEscapingFields)) {
            if (isset($disableEscapingFields[1]) && isset($disableEscapingFields[1][0])) {
                $disableEscapingFields = explode(",", $disableEscapingFields[1][0]);
            }
        }
        // Convert to XML first
        $convertedData = Mage::getModel('xtento_productexport/output_xml', array('profile' => $this->getProfile(), 'escape_special_chars' => $escapeSpecialChars, 'disable_escaping_fields' => $disableEscapingFields))->convertData($exportArray);
        // Get "first" file from returned data.
        $convertedXml = array_pop($convertedData);
        $xmlDoc = new DOMDocument;
        if (!$xmlDoc->loadXML($convertedXml, $libxmlConstants)) {
            $this->_throwXmlException(Mage::helper('xtento_productexport')->__("Could not load internally processed XML. Bad data maybe?"));
        }
        // Load different file templates
        $outputFormatMarkup = $this->getProfile()->getXslTemplate();
        if (empty($outputFormatMarkup)) {
            Mage::throwException(Mage::helper('xtento_productexport')->__('Error: No XSL Template has been set up for this export profile. Please open the export profile and set up your XSL Template in the "Output Format" tab.'));
        }
        try {
            $outputFormatXml = new SimpleXMLElement($outputFormatMarkup, null, strpos($outputFormatMarkup, '<') === false);
        } catch (Exception $e) {
            $this->_throwXmlException(Mage::helper('xtento_productexport')->__("Please repair the XSL Template of this profile. You need to have a valid XSL Template in order to export products. Could not load XSL Template:"));
        }
        $outputFormats = $outputFormatXml->xpath('//files/file');
        if (empty($outputFormats)) {
            Mage::throwException(Mage::helper('xtento_productexport')->__('No <files><file></file></files> markup found in XSL Template. Please repair your XSL Template.'));
        }
        // Loop through each <file> node
        foreach ($outputFormats as $outputFormat) {
            $fileAttributes = $outputFormat->attributes();
            $filename = $this->_replaceFilenameVariables($this->_getSimpleXmlElementAttribute($fileAttributes->filename), $exportArray);
            $fileType = $this->_getSimpleXmlElementAttribute($fileAttributes->type); // Currently supported: xsl (default), invoice_pdf, packingslip_pdf

            if (!$fileType || empty($fileType) || $fileType == 'xsl') {
                $charsetEncoding = $this->_getSimpleXmlElementAttribute($fileAttributes->encoding);
                $charsetLocale = $this->_getSimpleXmlElementAttribute($fileAttributes->locale);
                $searchCharacters = $this->_getSimpleXmlElementAttribute($fileAttributes->search);
                $replaceCharacters = $this->_getSimpleXmlElementAttribute($fileAttributes->replace);
                $quoteHandling = $this->_getSimpleXmlElementAttribute($fileAttributes->quotes);

                $xslTemplate = current($outputFormat->xpath('*'))->asXML();
                $xslTemplate = $this->_preparseXslTemplate($xslTemplate);

                // XSL Template
                $xslTemplateObj = new XSLTProcessor();
                $xslTemplateObj->registerPHPFunctions();
                // Add some parameters accessible as $variables in the XSL Template (example: <xsl:value-of select="$exportid"/>)
                $this->_addVariablesToXSLT($xslTemplateObj, $exportArray, $xslTemplate);
                // Import stylesheet
                /* Alternative DOMDocument version for versions that don't like SimpleXMLElements in importStylesheet */
                /*
                $domDocument = new DOMDocument();
                $domDocument->loadXML($xslTemplate);
                $xslTemplateObj->importStylesheet($domDocument);
                */
                $xslTemplateObj->importStylesheet(new SimpleXMLElement($xslTemplate));
                if (libxml_get_last_error() !== FALSE) {
                    $this->_throwXmlException(Mage::helper('xtento_productexport')->__("Please repair the XSL Template of this profile. There was a problem processing the XSL Template:"));
                }

                $adjustedXml = false;
                // Replace certain characters
                if (!empty($searchCharacters)) {
                    $this->_searchCharacters = str_split(str_replace(array('quote'), array('"'), $searchCharacters));
                    if (in_array('"', $this->_searchCharacters)) {
                        $replacePosition = array_search('"', $this->_searchCharacters);
                        if ($replacePosition !== false) {
                            $this->_searchCharacters[$replacePosition] = '&quot;';
                        }
                    }
                    $this->_replaceCharacters = str_split($replaceCharacters);
                    $adjustedXml = preg_replace_callback('/<(.*)>(.*)<\/(.*)>/um', array($this, '_replaceCharacters'), $convertedXml);
                }
                // Handle quotes in field data
                if (!empty($quoteHandling)) {
                    $ampSign = '&';
                    if ($escapeSpecialChars) {
                        $ampSign = '&amp;';
                    }
                    if ($quoteHandling == 'double') {
                        $quoteReplaceData = $ampSign . 'quot;' . $ampSign . 'quot;';
                    } else if ($quoteHandling == 'remove') {
                        $quoteReplaceData = '';
                    } else {
                        $quoteReplaceData = $quoteHandling;
                    }
                    if ($adjustedXml !== false) {
                        $adjustedXml = str_replace($ampSign . "quot;", $quoteReplaceData, $adjustedXml);
                    } else {
                        $adjustedXml = str_replace($ampSign . "quot;", $quoteReplaceData, $convertedXml);
                    }
                }
                if ($adjustedXml !== false) {
                    $xmlDoc->loadXML($adjustedXml, $libxmlConstants);
                }

                $outputBeforeEncoding = @$xslTemplateObj->transformToXML($xmlDoc);
                $output = $this->_changeEncoding($outputBeforeEncoding, $charsetEncoding, $charsetLocale);
                if (!$output && !empty($outputBeforeEncoding)) {
                    $this->_throwXmlException(Mage::helper('xtento_productexport')->__("Please repair the XSL Template of this profile, check the encoding tag, or make sure output has been generated by this template. No output has been generated."));
                }
                $outputArray[$filename] = $output;
            }
        }
        // Reset libxml settings
        libxml_use_internal_errors($useInternalXmlErrors);
        if (function_exists('libxml_disable_entity_loader')) {
            #libxml_disable_entity_loader($loadXmlEntities);
        }
        // Return generated files
        return $outputArray;
    }

    protected function _getSimpleXmlElementAttribute($data)
    {
        $current = @current($data);
        if ($current === false) {
            $stringData = (string)$data;
            if (isset($data[0])) {
                return $data[0];
            } else if ($stringData !== '') {
                return $stringData;
            }
        }
        return $current;
    }

    protected function _replaceCharacters($matches)
    {
        return "<$matches[1]>" . str_replace($this->_searchCharacters, $this->_replaceCharacters, $matches[2]) . "</$matches[3]>";
    }

    protected function _addVariablesToXSLT(XSLTProcessor $xslTemplateObj, $exportArray, $xslTemplateXml)
    {
        if ($this->_isRequiredInXslTemplate('$totalitemcount', $xslTemplateXml)) {
            // Total item count
            $xslTemplateObj->setParameter('', 'totalitemcount', $this->getVariableValue('total_item_count', $exportArray));
        }
        // Collection count
        $xslTemplateObj->setParameter('', 'collectioncount', $this->getVariableValue('collection_count', $exportArray));
        // Export ID
        $xslTemplateObj->setParameter('', 'exportid', $this->getVariableValue('export_id', $exportArray));
        // Date information
        $xslTemplateObj->setParameter('', 'dateFromTimestamp', $this->getVariableValue('date_from_timestamp', $exportArray));
        $xslTemplateObj->setParameter('', 'dateToTimestamp', $this->getVariableValue('date_to_timestamp', $exportArray));
        // Current timestamp
        if ($this->_isRequiredInXslTemplate('$timestamp', $xslTemplateXml)) {
            $xslTemplateObj->setParameter('', 'timestamp', Mage::getModel('core/date')->timestamp(time()));
        }
        // Root category ID for store that is exported
        if ($this->_isRequiredInXslTemplate('$rootCategoryId', $xslTemplateXml)) {
            $xslTemplateObj->setParameter('', 'rootCategoryId', $this->getVariableValue('root_category_id', $exportArray));
        }
        return $this;
    }

    /*
     * Check if the variable is used in the XSL Template and only if yes return true
     */
    protected function _isRequiredInXslTemplate($variable, $xslTemplateXml)
    {
        if (strpos($xslTemplateXml, $variable) === false) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * Many old XSL Templates are still using orders/order. Replace with objects/object on the fly.
     */
    private function _preparseXslTemplate($xslTemplate)
    {
        return str_replace('<xsl:for-each select="products/product">', '<xsl:for-each select="objects/object">', $xslTemplate);
    }
}