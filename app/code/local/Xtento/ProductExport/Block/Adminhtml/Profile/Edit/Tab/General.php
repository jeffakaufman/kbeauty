<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-09-24T17:23:44+02:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/General.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_General extends Xtento_ProductExport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $model = Mage::registry('product_export_profile');
        if ($model->getId() && !$model->getEnabled()) {
            $formMessages[] = array('type' => 'warning', 'message' => Mage::helper('xtento_productexport')->__('This profile is disabled. No automatic exports will be made and the profile won\'t show up for manual exports.'));
        }
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('product_export_profile');
        // Set default values
        if (!$model->getId()) {
            $model->setEnabled(1);
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('xtento_productexport')->__('General Configuration'),
        ));

        if ($model->getId()) {
            $fieldset->addField('profile_id', 'hidden', array(
                'name' => 'profile_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Name'),
            'name' => 'name',
            'required' => true,
        ));

        if ($model->getId()) {
            $fieldset->addField('enabled', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Enabled'),
                'name' => 'enabled',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
            ));
        }

        $entity = $fieldset->addField('entity', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Export Type'),
            'name' => 'entity',
            'options' => Mage::getSingleton('xtento_productexport/system_config_source_export_entity')->toOptionArray(),
            'required' => true,
            'note' => Mage::helper('xtento_productexport')->__('This setting can\'t be changed after creating the profile. Add a new profile for different export types.')
        ));
        if ($model->getId() && !Mage::getSingleton('adminhtml/session')->getProfileDuplicated()) {
            // 1.3 Compatibility. Does not accept the disabled param directly in the addField array.
            $entity->setDisabled(true);
        }


        if (!Mage::registry('product_export_profile') || !Mage::registry('product_export_profile')->getId()) {
            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }

        if (Mage::registry('product_export_profile') && Mage::registry('product_export_profile')->getId()) {
            $fieldset = $form->addFieldset('advanced_fieldset', array(
                'legend' => Mage::helper('xtento_productexport')->__('Export Settings'),
                'class' => 'fieldset-wide',
            ));

            $fieldset->addField('save_files_local_copy', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Save local copies of exports'),
                'name' => 'save_files_local_copy',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If set to yes, local copies of the exported files will be saved in the ./var/product_export_bkp/ folder. If set to no, you won\'t be able to download old export files from the export/execution log.')
            ));

            $fieldset->addField('export_one_file_per_object', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Export each %s separately', Mage::registry('product_export_profile')->getEntity()),
                'name' => 'export_one_file_per_object',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If set to yes, each %s exported would be saved in a separate file. This means, for every %s you export, one file will be created, with just the one %s in there. If set to no, one file will be created with all the exported %s in there.', Mage::registry('product_export_profile')->getEntity(), Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()), Mage::registry('product_export_profile')->getEntity(), Mage::helper('xtento_productexport/entity')->getPluralEntityName(Mage::registry('product_export_profile')->getEntity()))
            ));

            $fieldset->addField('export_strip_tags', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Remove HTML in exported data'),
                'name' => 'export_strip_tags',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If set to yes, HTML/XML tags will get removed from any exported data using the PHP function strip_tags.')
            ));

            $fieldset->addField('export_replace_nl_br', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Replace new lines in attribute values'),
                'name' => 'export_replace_nl_br',
                'values' => array(
                    array('value' => 3, 'label' => Mage::helper('adminhtml')->__('Remove new lines from attribute values')),
                    array('value' => 2, 'label' => Mage::helper('adminhtml')->__('Replace with a space')),
                    array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Replace with <br />')),
                    array('value' => 0, 'label' => Mage::helper('adminhtml')->__('No replacing')),
                ),
                'note' => Mage::helper('xtento_productexport')->__('If enabled, new lines (\n, \r, \r\n) in attribute values will be replaced.')
            ));

            $fieldset->addField('export_url_remove_store', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Remove store parameter from URL'),
                'name' => 'export_url_remove_store',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If set to yes, the "___store" parameter will be removed from exported product URLs. Only use this if you don\'t want the ___store parameter to show up in your product URLs.')
            ));
        }

        $form->setValues($model->getData());

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('catalog')->__('Continue'),
                'onclick' => "saveAndContinueEdit()",
                'class' => 'save'
            ))
        );
        return parent::_prepareLayout();
    }
}