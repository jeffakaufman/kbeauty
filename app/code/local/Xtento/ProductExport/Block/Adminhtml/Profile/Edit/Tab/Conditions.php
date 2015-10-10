<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2015-03-12T15:28:35+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/Conditions.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_Conditions extends Xtento_ProductExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_productexport')->__('The settings specified below will be applied to all manual and automatic exports. For manual exports, this can be changed in the "Manual Export" screen before exporting. If a %s does not match the filters, it simply won\'t be exported.', Mage::registry('product_export_profile')->getEntity()));
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('product_export_profile');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('store', array('legend' => Mage::helper('xtento_productexport')->__('Store View &amp; Customer Group'), 'class' => 'fieldset-wide'));

        $fieldset->addField('store_ids', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Store View'),
            'name' => 'store_ids[]',
            'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_productexport')->__('--- Default Values ---'))), Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()),
            'note' => Mage::helper('xtento_productexport')->__('Values for attributes retrieved for %s will be fetched from this store view. <br/><strong>Attention: If no store view is selected, no SEO / rewritten %s URLs will be exported, as the extension doesn\'t know which store the %s you\'re exporting belongs to. It\'s recommended to set a store view here.</strong>', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()), Mage::registry('product_export_profile')->getEntity(), Mage::registry('product_export_profile')->getEntity()),
        ));

        $fieldset->addField('customer_group_id', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Customer Group'),
            'name' => 'customer_group_id',
            'values' => array_merge_recursive(array(array('value' => '0', 'label' => Mage::helper('xtento_productexport')->__('--- Not logged in ---'))), Mage::getResourceModel('customer/group_collection')->addFieldToFilter('customer_group_id', array('gt' => 0))->load()->toOptionHash()),
            'note' => Mage::helper('xtento_productexport')->__('Prices will be fetched for this customer group.'),
        ));

        $fieldset = $form->addFieldset('object_filters', array('legend' => Mage::helper('xtento_productexport')->__('%s Filters', ucwords($model->getEntity())), 'class' => 'fieldset-wide'));

        $fieldset->addField('export_filter_datefrom', 'date', array(
            'label' => Mage::helper('xtento_productexport')->__('Date From'),
            'name' => 'export_filter_datefrom',
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'note' => Mage::helper('xtento_productexport')->__('Export only %s created after date X (including day X).', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()))
        ));

        $fieldset->addField('export_filter_dateto', 'date', array(
            'label' => Mage::helper('xtento_productexport')->__('Date To'),
            'name' => 'export_filter_dateto',
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'note' => Mage::helper('xtento_productexport')->__('Export only %s created before date X (including day X).', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()))
        ));

        $fieldset->addField('export_filter_last_x_days', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Created during the last X days'),
            'name' => 'export_filter_last_x_days',
            'maxlength' => 5,
            'style' => 'width: 50px !important;" min="1',
            'note' => Mage::helper('xtento_productexport')->__('Export only %s created during the last X days (including day X). Only enter numbers here, nothing else. Leave empty if no "created during the last X days" filter should be applied.', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()))
        ))->setType('number');

        $fieldset->addField('export_filter_updated_last_x_minutes', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Updated during the last X minutes'),
            'name' => 'export_filter_updated_last_x_minutes',
            'maxlength' => 5,
            'style' => 'width: 50px !important;" min="1',
            'note' => Mage::helper('xtento_productexport')->__('Export only %s updated during the last X minutes. Only enter numbers here, nothing else. Leave empty if no "created during the last X days" filter should be applied.', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()))
        ))->setType('number');

        $fieldset->addField('export_filter_new_only', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Export only new %s', Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity())),
            'name' => 'export_filter_new_only',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_productexport')->__('Regardless whether you\'re using manual, cronjob or the event-based export, if set to yes, this setting will make sure every %s gets exported only ONCE by this profile. This means, even if another export event gets called, if the %s has been already exported by this profile, it won\'t be exported again. You can "reset" exported objects in the "Profile Export History" tab.', Mage::registry('product_export_profile')->getEntity(), Mage::registry('product_export_profile')->getEntity())
        ));


        if (Mage::registry('product_export_profile')->getEntity() == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            $fieldset = $form->addFieldset('item_filters', array('legend' => Mage::helper('xtento_productexport')->__('Advanced Product Filters'), 'class' => 'fieldset-wide'));

            $fieldset->addField('export_filter_instock_only', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Export *only* in stock products'),
                'name' => 'export_filter_instock_only',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If set to yes, only products which are in stock will be exported.')
            ));

            $visibilityValues = Mage::getModel('catalog/product_visibility')->getAllOptions();
            array_shift($visibilityValues);
            $fieldset->addField('export_filter_product_visibility', 'multiselect', array(
                'label' => Mage::helper('xtento_productexport')->__('Product visibilities to export'),
                'name' => 'export_filter_product_visibility',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_productexport')->__('--- All product visibilities ---'))), $visibilityValues),
                'note' => Mage::helper('xtento_productexport')->__('Only products where the selected visibility value matches will be exported.')
            ));

            $statusValues = Mage::getModel('catalog/product_status')->getAllOptions();
            array_shift($statusValues);
            $fieldset->addField('export_filter_product_status', 'multiselect', array(
                'label' => Mage::helper('xtento_productexport')->__('Product status to export'),
                'name' => 'export_filter_product_status',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_productexport')->__('--- All product statuses ---'))), $statusValues),
                'note' => Mage::helper('xtento_productexport')->__('Only products where the selected status value matches will be exported.')
            ));

            $fieldset->addField('export_filter_product_type', 'multiselect', array(
                'label' => Mage::helper('xtento_productexport')->__('Hidden Product Types'),
                'name' => 'export_filter_product_type',
                'values' => array_merge_recursive(array(array('value' => '', 'label' => Mage::helper('xtento_productexport')->__('--- No hidden product types ---'))), Mage::getModel('catalog/product_type')->getOptions()),
                'note' => Mage::helper('xtento_productexport')->__('The selected product types won\'t be exported and won\'t show up in the output format for this profile. You can still fetch information from the parent product in the XSL Template using the <i>parent_item/</i> node. ')
            ));

            $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('*/productexport_profile/newConditionHtml/form/rule_conditions_fieldset', array('profile_id' => Mage::registry('product_export_profile')->getId())));

            $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
                'legend' => Mage::helper('xtento_productexport')->__('Additional filters: Export %s only if the following conditions are met (Attention: When exporting many products, set up the filter in the XSL Template - much faster)', Mage::registry('product_export_profile')->getEntity()),
            ))->setRenderer($renderer);

            $fieldset->addField('conditions', 'text', array(
                'name' => 'conditions',
                'label' => Mage::helper('salesrule')->__('Conditions'),
                'title' => Mage::helper('salesrule')->__('Conditions'),
            ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

            $fieldset = $form->addFieldset('performance_settings', array('legend' => Mage::helper('xtento_productexport')->__('Performance Settings'), 'class' => 'fieldset-wide'));

            $fieldset->addField('performance_note', 'note', array(
                'text' => Mage::helper('xtento_productexport')->__('This can be used to speed up the export. Only the attributes selected below will be made available when exporting then. This is especially helpful if you have a lot product attributes.')
            ));

            $availableAttributes = array(array('value' => '', 'label' => Mage::helper('xtento_productexport')->__('--- All attributes ---')));
            $productAttributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setOrder('main_table.frontend_label', 'asc')
                ->load();
            foreach ($productAttributes as $productAttribute) {
                if ($productAttribute->getFrontendLabel()) {
                    $availableAttributes[] = array('label' => sprintf("%s [%s]", $productAttribute->getFrontendLabel(), $productAttribute->getAttributeCode()), 'value' => $productAttribute->getAttributeCode());
                }
            }
            $fieldset->addField('attributes_to_select', 'multiselect', array(
                'label' => Mage::helper('xtento_productexport')->__('Product attributes to export'),
                'name' => 'attributes_to_select',
                'values' => $availableAttributes,
                'note' => Mage::helper('xtento_productexport')->__('Select multiple attributes using the CTRL/SHIFT buttons on your keyboard.'),
                'style' => 'width: auto; max-width: 500px;'
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
