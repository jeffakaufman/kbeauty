<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-11-26T20:13:39+01:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tab/Settings.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_Settings extends Xtento_StockImport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_stockimport')->__('The settings specified below will be applied to all manual and automatic imports.'));
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('stock_import_profile');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('settings', array('legend' => Mage::helper('xtento_stockimport')->__('Import Settings'), 'class' => 'fieldset-wide',));

        /*$fieldset->addField('save_files_local_copy', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Save local copy of imported file'),
            'name' => 'save_files_local_copy',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('If set to yes, local copies of imported files will be saved in the ./var/import_bkp/ folder. If set to no, you won\'t be able to download old imported files from the import/execution log.')
        ));*/

        $fieldset->addField('reindex_mode', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Reindex mode'),
            'name' => 'reindex_mode',
            'values' => Mage::getSingleton('xtento_stockimport/system_config_source_stock_reindex')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('No reindex is required in most cases.')
        ));
        $fieldset->addField('mark_out_of_stock', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('If stock qty is below "Qty for Item\'s Status to become Out of Stock", mark as out of stock'),
            'name' => 'mark_out_of_stock',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('This value will be overridden if you map the "Stock Status" field')
        ));
        $fieldset->addField('import_relative_stock_level', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Import relative stock level'),
            'name' => 'import_relative_stock_level',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('If set to "No", the stock level specified in the import file will be imported, whatever it is. If set to "Yes", if you import +5, 5 will be added to the current stock level, and if you import -5, 5 will be subtracted from the current stock level.')
        ));
        $fieldset->addField('product_identifier', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Product Identifier'),
            'name' => 'product_identifier',
            'values' => Mage::getSingleton('xtento_stockimport/system_config_source_stock_identifier')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('This is what will be called the Product Identifier in the import settings and is what\'s used to identify the product in the import file. Almost always you will want to use the SKU. Using a custom attribute can be slower as well.')
        ));
        $attributeCodeJs = "<script>Event.observe(window, 'load', function() { function checkAttributeField(field) {if(field.value=='attribute') {\$('product_identifier_attribute_code').parentNode.parentNode.show()} else {\$('product_identifier_attribute_code').parentNode.parentNode.hide()}} checkAttributeField($('product_identifier')); $('product_identifier').observe('change', function(){ checkAttributeField(this); }); });</script>";
        if ($model->getData('product_identifier') !== 'attribute') {
            // Not filled
            $attributeCodeJs .= "<script>$('product_identifier_attribute_code').parentNode.parentNode.hide()</script>";
        }
        $fieldset->addField('product_identifier_attribute_code', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Product Identifier: Attribute Code'),
            'name' => 'product_identifier_attribute_code',
            'note' => Mage::helper('xtento_stockimport')->__('IMPORTANT: This is not the attribute name. It is the attribute code you assigned the attribute.') . $attributeCodeJs,
        ));

        $fieldset = $form->addFieldset('misc_settings', array('legend' => Mage::helper('xtento_stockimport')->__('Miscellaneous Settings'), 'class' => 'fieldset-wide'));

        if (Mage::helper('xtcore/utils')->getIsPEorEE()) {
            $fieldset->addField('enterprise_fpc_action', 'select', array(
                'label' => Mage::helper('xtento_stockimport')->__('Action after importing'),
                'name' => 'enterprise_fpc_action',
                'values' => array(array('value' => '', 'label' => Mage::helper('xtento_stockimport')->__('--- Don\'t do anything ---')), array('value' => 'invalidate', 'label' => Mage::helper('xtento_stockimport')->__('Invalidate Full Page Cache')), array('value' => 'clean', 'label' => Mage::helper('xtento_stockimport')->__('Clean Full Page Cache'))),
                'note' => Mage::helper('xtento_stockimport')->__('If you want to invalidate or clean the built-in Magento Enterprise full page cache after importing using this profile, select the appropriate action here.'),
            ));
        }
        $fieldset->addField('update_low_stock_date', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Update "low stock date" after importing'),
            'name' => 'update_low_stock_date',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('May make the import slower. Only enable if required.')
        ));


        if (Xtento_StockImport_Model_Import_Entity_Stock::$importPrices || Xtento_StockImport_Model_Import_Entity_Stock::$importSpecialPrices) {
            $fieldset = $form->addFieldset('store', array('legend' => Mage::helper('xtento_stockimport')->__('Store View (Price Update)'), 'class' => 'fieldset-wide'));

            $fieldset->addField('price_update_store_id', 'multiselect', array(
                'label' => Mage::helper('xtento_stockimport')->__('Store View'),
                'name' => 'price_update_store_id',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_stockimport')->__('--- Global (all) ---'))), Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()),
                'note' => Mage::helper('xtento_stockimport')->__('The price will be set for the following website/store id.'),
            ));
        }

        $this->setTemplate('xtento/stockimport/settings.phtml');

        $configuration = $model->getConfiguration();
        if (empty($configuration)) {
            // Set default values
            $configuration['mark_out_of_stock'] = true;
        }
        $form->setValues($configuration);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
