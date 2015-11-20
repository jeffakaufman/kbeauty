<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-04-13T19:19:21+02:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tabs.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('profile_tabs');
        $this->setDestElementId('edit_form');
        if (!Mage::registry('product_export_profile')) {
            $this->setTitle(Mage::helper('xtento_productexport')->__('Export Profile'));
        } else {
            $this->setTitle(Mage::helper('xtento_productexport')->__('%s Export Profile', ucfirst(Mage::registry('product_export_profile')->getEntity())));
        }
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('xtento_productexport')->__('General Configuration'),
            'title' => Mage::helper('xtento_productexport')->__('General Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_general')->toHtml(),
        ));

        if (!Mage::registry('product_export_profile') || !Mage::registry('product_export_profile')->getId()) {
            // We just want to display the "General" tab to set the export type for new profiles
            return parent::_beforeToHtml();
        }

        $this->addTab('destination', array(
            'label' => Mage::helper('xtento_productexport')->__('Export Destinations'),
            'title' => Mage::helper('xtento_productexport')->__('Export Destinations'),
            'url' => $this->getUrl('*/*/destination', array('_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('output', array(
            'label' => Mage::helper('xtento_productexport')->__('Output Format'),
            'title' => Mage::helper('xtento_productexport')->__('Output Format'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_output')->toHtml(),
        ));

        $this->addTab('conditions', array(
            'label' => Mage::helper('xtento_productexport')->__('Store & Filters'),
            'title' => Mage::helper('xtento_productexport')->__('Store & Filters'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_conditions')->toHtml(),
        ));

        $this->addTab('manual', array(
            'label' => Mage::helper('xtento_productexport')->__('Manual Export'),
            'title' => Mage::helper('xtento_productexport')->__('Manual Export'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_manual')->toHtml(),
        ));

        $this->addTab('automatic', array(
            'label' => Mage::helper('xtento_productexport')->__('Automatic Export'),
            'title' => Mage::helper('xtento_productexport')->__('Automatic Export'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_automatic')->toHtml(),
        ));

        $this->addTab('log', array(
            'label' => Mage::helper('xtento_productexport')->__('Profile Execution Log'),
            'title' => Mage::helper('xtento_productexport')->__('Profile Execution Log'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_log')->toHtml(),
        ));

        $this->addTab('history', array(
            'label' => Mage::helper('xtento_productexport')->__('Profile Export History'),
            'title' => Mage::helper('xtento_productexport')->__('Profile Export History'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tab_history')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}