<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-08-12T16:41:23+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tabs.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('profile_tabs');
        $this->setDestElementId('edit_form');
        if (!Mage::registry('stock_import_profile')) {
            $this->setTitle(Mage::helper('xtento_stockimport')->__('Import Profile'));
        } else {
            $this->setTitle(Mage::helper('xtento_stockimport')->__('%s Import Profile', ucfirst(Mage::registry('stock_import_profile')->getEntity())));
        }
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('xtento_stockimport')->__('General Configuration'),
            'title' => Mage::helper('xtento_stockimport')->__('General Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_general')->toHtml(),
        ));

        if (!Mage::registry('stock_import_profile') || !Mage::registry('stock_import_profile')->getId()) {
            // We just want to display the "General" tab to set the import entity for new profiles
            return parent::_beforeToHtml();
        }

        $this->addTab('settings', array(
            'label' => Mage::helper('xtento_stockimport')->__('Import Settings'),
            'title' => Mage::helper('xtento_stockimport')->__('Import Settings'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_settings')->toHtml(),
        ));

        $this->addTab('mapping', array(
            'label' => Mage::helper('xtento_stockimport')->__('File Configuration'),
            'title' => Mage::helper('xtento_stockimport')->__('File Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_mapping')->toHtml(),
        ));

        $this->addTab('source', array(
            'label' => Mage::helper('xtento_stockimport')->__('Import Sources'),
            'title' => Mage::helper('xtento_stockimport')->__('Import Sources'),
            'url' => $this->getUrl('*/*/source', array('_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('automatic', array(
            'label' => Mage::helper('xtento_stockimport')->__('Automatic Import'),
            'title' => Mage::helper('xtento_stockimport')->__('Automatic Import'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_automatic')->toHtml(),
        ));

        $this->addTab('log', array(
            'label' => Mage::helper('xtento_stockimport')->__('Profile Execution Log'),
            'title' => Mage::helper('xtento_stockimport')->__('Profile Execution Log'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_log')->toHtml(),
        ));

        /*$this->addTab('history', array(
            'label' => Mage::helper('xtento_stockimport')->__('Profile Import History'),
            'title' => Mage::helper('xtento_stockimport')->__('Profile Import History'),
            'content' => $this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tab_history')->toHtml(),
        ));*/

        return parent::_beforeToHtml();
    }
}