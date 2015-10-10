<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-07-21T15:10:12+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Importfields.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_Mapping_Importfields extends Mage_Core_Block_Abstract
{
    public function _toHtml()
    {
        $htmlId = 'select_#{_id}';
        $html = '<select id="'.$htmlId.'" name="' . $this->getInputName() . '" class="select" style="'.$this->getStyle().'" onchange="'.$this->_getSelectOnChangeJs().'" onmouseover="'.$this->_getSelectBeforeClickJs().'">'.$this->_getImportFields().'<\\/select>';

        // Select the pre-mapped field
        $html .= <<<JS
        <script>
            if ({$this->getMappingId()}_mapping_values[\'#{_id}\']) {
                $(\'{$htmlId}\').setValue({$this->getMappingId()}_mapping_values[\'#{_id}\']);
            }

            if ($(\'{$htmlId}\').options[$(\'{$htmlId}\').selectedIndex].hasClassName(\'default-value-disabled\')) {
                $(\'{$this->getMappingId()}[#{_id}][default_value]\').value = \'\';
                $(\'{$this->getMappingId()}[#{_id}][default_value]\').disable();
                $(\'{$this->getMappingId()}[#{_id}][default_value]\').style.backgroundColor = \'#f0f0f0\';
            }
        <\/script>
JS;

        return str_replace(array("\r", "\n", "\r\n"), "", $html);
    }

    public function _getImportFields() {
        $html = '<option value="" selected="selected">--- Select field ---<\\/option>';
        foreach ($this->getImportFields() as $code => $field) {
            $disabled = '';
            if (isset($field['disabled']) && $field['disabled']) {
                $disabled = ' disabled="disabled"';
            }
            $tooltipJs = '';
            if (isset($field['tooltip']) && $field['tooltip']) {
                $tooltipJs = ' onmouseover="'.$this->_getOptionOnHoverJs($field['tooltip']).'"';
            }
            $className = '';
            if (isset($field['default_value_disabled']) && $field['default_value_disabled']) {
                $className = ' class="default-value-disabled"';
            }
            $html .= '<option value="'.$code.'"'.$disabled.$tooltipJs.$className.'>'.$field['label'].'<\\/option>';
        }
        return $html;
    }

    public function _getSelectOnChangeJs() {
        $inputNameDefaultValues = str_replace('[field]', '[default_value]', $this->getInputName());
        $js = <<<JS
if (this.value !== \'\') {
    for (var i in {$this->getMappingId()}_mapping_values) {
        if ({$this->getMappingId()}_mapping_values[i] == this.value) {
            alert(\'{$this->__('You have already mapped that field. Make sure to remove the other mapping before trying to map this field again.')}\');
            this.value = \'\';
            {$this->getMappingId()}_mapping_values[\'#{_id}\'] = \'\';
            return false;
        }
    }
}
if ($(\'{$this->getMappingId()}[#{_id}][default_value]\')) {
    if (this.options[this.selectedIndex].hasClassName(\'default-value-disabled\')) {
        $(\'{$this->getMappingId()}[#{_id}][default_value]\').value = \'\';
        $(\'{$this->getMappingId()}[#{_id}][default_value]\').disable();
        $(\'{$this->getMappingId()}[#{_id}][default_value]\').style.backgroundColor = \'#f0f0f0\';
    } else {
        $(\'{$this->getMappingId()}[#{_id}][default_value]\').disabled = false;
        $(\'{$this->getMappingId()}[#{_id}][default_value]\').style.backgroundColor = \'#fff\';
    }
}
{$this->getMappingId()}_mapping_values[\'#{_id}\'] = this.value;

var default_values = {$this->getMappingId()}_possible_default_values.get(this.value);
if (default_values) {
    if ($(\'{$this->getMappingId()}[#{_id}][default_value]\')) {
        var field = $(\'{$this->getMappingId()}[#{_id}][default_value]\').parentNode;
    } else if ($(\'select_default_#{_id}\')) {
        var field = $(\'select_default_#{_id}\').parentNode;
    } else {
        return;
    }
    field.innerHTML = \'\';
    select = document.createElement(\'select\');
    select.setAttribute(\'style\', \'width: 99.9%;\');
    select.setAttribute(\'id\', \'select_default_#{_id}\');
    select.setAttribute(\'name\', \'{$inputNameDefaultValues}\');
    select.setAttribute(\'class\', \'select\');
    option = document.createElement(\'option\');
    optionText = document.createTextNode(\'{$this->__('--- Select value ---')}\');
    option.appendChild(optionText);
    option.setAttribute(\'value\', \'\');
    select.appendChild(option);
    \$H(default_values).each(function(pair) {
        option = document.createElement(\'option\');
        optionText = document.createTextNode(pair.value);
        option.appendChild(optionText);
        option.setAttribute(\'value\', pair.key);
        select.appendChild(option);
    });
    field.appendChild(select);
} else {
    if ($(\'select_default_#{_id}\')) {
        var field = $(\'select_default_#{_id}\').parentNode;
        field.innerHTML = \'\';
        input = document.createElement(\'input\');
        input.setAttribute(\'type\', \'text\');
        input.setAttribute(\'id\', \'{$inputNameDefaultValues}\');
        input.setAttribute(\'name\', \'{$inputNameDefaultValues}\');
        input.setAttribute(\'value\', \'\');
        input.setAttribute(\'class\', \'input-text\');
        input.setAttribute(\'style\', \'width: 98%;\');
        field.appendChild(input);
    }
}
JS;
        return str_replace(array("\r", "\n", "\r\n"), "", $js);
    }


    public function _getSelectBeforeClickJs() {
        $js = <<<JS
  for (i=0; i<this.options.length; i++) {
    if (this.options[i].innerHTML.include(\'-- \') || this.value == this.options[i].value) {
        continue;
    }
    var hasValue = false;
    for (var i2 in {$this->getMappingId()}_mapping_values) {
        if ({$this->getMappingId()}_mapping_values[i2] == this.options[i].value) {
            hasValue = true;
        }
    }
    if (hasValue) {
        $(this.options[i]).disabled = true;
    } else {
        $(this.options[i]).disabled = false;
    }
  }
JS;
        return str_replace(array("\r", "\n", "\r\n"), "", $js);
    }

    public function _getOptionOnHoverJs($tooltip) {
        return "";

        $js = <<<JS
if ($(\'row_stockimport_general_import_mapping_comment\')) {
    $(\'row_stockimport_general_import_mapping_comment\').innerHTML = \'{$tooltip}\';
    $(\'row_stockimport_general_import_mapping_comment\').show();
}
JS;
        return str_replace(array("\r", "\n", "\r\n"), "", $js);
    }
}
