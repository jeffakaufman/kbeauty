<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-11-18T11:40:00+01:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_Mapping_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $mappingModel = Mage::getModel('xtento_stockimport/processor_mapping_fields');
        $profile = Mage::registry('stock_import_profile');
        $configuration = $profile->getConfiguration();
        $mappingModel->setMappingData(isset($configuration['mapping']) ? $configuration['mapping'] : array());

        $importFieldRenderer = Mage::app()->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_mapping_importfields');
        $importFieldRenderer->setImportFields($mappingModel->getMappingFields());
        $importFieldRenderer->setMappingId($this->MAPPING_ID);
        $importFieldRenderer->setStyle('width: 99.9%');

        $this->addColumn('field', array(
            'label' => Mage::helper('adminhtml')->__('Import Field'),
            'style' => 'width: 99.9%',
            'renderer' => $importFieldRenderer
        ));

        $this->addColumn('value', array(
            'label' => Mage::helper('adminhtml')->__($this->VALUE_FIELD_NAME),
            'style' => 'width:98%',
        ));

        $defaultValuesRenderer = Mage::app()->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_mapping_defaultvalues');
        $defaultValuesRenderer->setImportFields($mappingModel->getMappingFields());
        $defaultValuesRenderer->setMappingModel($mappingModel);
        $defaultValuesRenderer->setMappingId($this->MAPPING_ID);
        $defaultValuesRenderer->setStyle('width: 99.9%');

        $this->addColumn('default_value', array(
            'label' => Mage::helper('adminhtml')->__('Default Value'),
            'style' => 'width: 98%',
            'renderer' => $defaultValuesRenderer
        ));

        $this->_addAfter = true;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add field to mapping');
        parent::__construct();
        $this->setTemplate('xtento/stockimport/widget/mapper.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $mappingModel = Mage::getModel('xtento_stockimport/processor_mapping_fields');
        $profile = Mage::registry('stock_import_profile');
        $configuration = $profile->getConfiguration();
        $mappingModel->setMappingData(isset($configuration['mapping']) ? $configuration['mapping'] : array());
        $mappingFields = $mappingModel->getMappingFields();

        // Add the actual mapped fields
        $html = '<script>' . "\n";
        $html .= 'var ' . $this->MAPPING_ID . '_mapping_values = new Hash();' . "\n";
        foreach ($mappingModel->getMapping() as $field => $fieldData) {
            $html .= $this->MAPPING_ID . '_mapping_values[\'' . $fieldData['id'] . '\'] = \'' . $this->_escapeStringJs($field) . '\';' . "\n";
        }
        // Add the default values
        $html .= 'var ' . $this->MAPPING_ID . '_default_values = new Hash();' . "\n";
        foreach ($mappingModel->getMapping() as $field => $fieldData) {
            $html .= $this->MAPPING_ID . '_default_values[\'' . $fieldData['id'] . '\'] = \'' . $this->_escapeStringJs($fieldData['default_value']) . '\';' . "\n";
        }
        // Add the possible default values
        $html .= 'var ' . $this->MAPPING_ID . '_possible_default_values = $H({' . "\n";
        $loopLength = 0;
        foreach ($mappingFields as $field => $fieldData) {
            if (isset($fieldData['default_values']) && is_array($fieldData['default_values'])) {
                $loopLength++;
            }
        }
        $loopCounter = 0;
        foreach ($mappingFields as $field => $fieldData) {
            if (isset($fieldData['default_values']) && is_array($fieldData['default_values'])) {
                $loopCounter++;
                $loopLength2 = count($fieldData['default_values']);
                $loopCounter2 = 0;
                $html .= '\''.$this->_escapeStringJs($field) . '\': {' . "\n";
                foreach ($fieldData['default_values'] as $code => $label) {
                    $loopCounter2++;
                    $html .= '\''.$this->_escapeStringJs($code) . '\': \'' . $this->_escapeStringJs($label) . '\'';
                    if ($loopCounter2 !== $loopLength2) {
                        $html .= ',';
                    }
                    $html .= "\n";
                }
                $html .= '}';
                if ($loopCounter !== $loopLength) {
                    $html .= ',';
                }
            }
        }
        $html .= '});';
        $html .= '</script>' . "\n";

        $html .= parent::render($element);

        return $html;
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" id="' . $inputName . '" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }

    private function _escapeStringJs($string) {
        return str_replace(array("'", "\n", "\r"), array("\\'", " ", " "), $string);
    }
}
