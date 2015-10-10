<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-07-20T18:50:22+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tab/General.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_General extends Xtento_StockImport_Block_Adminhtml_Widget_Tab
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $model = Mage::registry('stock_import_profile');
        if ($model->getId() && !$model->getEnabled()) {
            $formMessages[] = array('type' => 'warning', 'message' => Mage::helper('xtento_stockimport')->__('This profile is disabled. No automatic imports will be made and the profile won\'t show up for manual imports.'));
        }
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('stock_import_profile');
        // Set default values
        if (!$model->getId()) {
            $model->setEnabled(1);
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('xtento_stockimport')->__('General Configuration'),
        ));

        if ($model->getId()) {
            $fieldset->addField('profile_id', 'hidden', array(
                'name' => 'profile_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Name'),
            'name' => 'name',
            'required' => true,
        ));

        if ($model->getId()) {
            $fieldset->addField('enabled', 'select', array(
                'label' => Mage::helper('xtento_stockimport')->__('Enabled'),
                'name' => 'enabled',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
            ));
        }

        $processor = $fieldset->addField('processor', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('File Processor'),
            'name' => 'processor',
            'options' => Mage::getSingleton('xtento_stockimport/system_config_source_import_processor')->toOptionArray(),
            'required' => true,
            'note' => Mage::helper('xtento_stockimport')->__('This setting can\'t be changed after creating the profile. Add a new profile for different import types.')
        ));

        $entity = $fieldset->addField('entity', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Import Type'),
            'name' => 'entity',
            'options' => Mage::getSingleton('xtento_stockimport/system_config_source_import_entity')->toOptionArray(),
            'required' => true,
            'note' => Mage::helper('xtento_stockimport')->__('This setting can\'t be changed after creating the profile. Add a new profile for different import types.')
        ));
        if ($model->getId() && !Mage::getSingleton('adminhtml/session')->getProfileDuplicated()) {
            // 1.3 Compatibility. Does not accept the disabled param directly in the addField array.
            $entity->setDisabled(true);
            $processor->setDisabled(true);
        }

        if (!Mage::registry('stock_import_profile') || !Mage::registry('stock_import_profile')->getId()) {
            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }
/*
        if (Mage::registry('stock_import_profile') && Mage::registry('stock_import_profile')->getId()) {
            $fieldset = $form->addFieldset('advanced_fieldset', array(
                'legend' => Mage::helper('xtento_stockimport')->__('Import Settings'),
                'class' => 'fieldset-wide',
            ));

            $fieldset->addField('save_files_local_copy', 'select', array(
                'label' => Mage::helper('xtento_stockimport')->__('Save local copies of imports'),
                'name' => 'save_files_local_copy',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_stockimport')->__('If set to yes, local copies of the imported files will be saved in the ./var/import_bkp/ folder. If set to no, you won\'t be able to download old import files from the import/execution log.')
            ));
        }
*/
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