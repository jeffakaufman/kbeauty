<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-01-11T17:42:20+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Observer/Massaction.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Observer_Massaction extends Mage_Core_Model_Abstract
{
    /**
     * Add mass-actions to the catalog grids, the non-intrusive way.
     */
    public function core_block_abstract_prepare_layout_after($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (in_array($block->getRequest()->getControllerName(), $this->getControllerNames())) {
            $this->_addMassActions($block, Xtento_ProductExport_Model_Export::ENTITY_PRODUCT);
        }
    }

    private function _addMassActions($block, $type)
    {
        if (($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) && $this->_initBlocks() && in_array($block->getRequest()->getControllerName(), $this->getControllerNames($type))) {
            if (Mage::registry('moduleString') !== 'false') {
                return;
            }
            $isSecure = Mage::app()->getStore()->isCurrentlySecure() ? true : false;
            $block->addItem('xtento_' . $type . '_export', array(
                'label' => Mage::helper('xtento_productexport')->__('Export ' . ucfirst($type) . 's'),
                'url' => Mage::app()->getStore()->getUrl('adminhtml/productexport_manual/gridPost', array('_secure' => $isSecure, 'type' => $type)),
                'additional' => array(
                    'profile_id' => array(
                        'name' => 'profile_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('xtento_productexport')->__('Profile'),
                        'values' => Mage::getModel('xtento_productexport/system_config_source_export_profile')->toOptionArray(false, $type)
                    )
                )
            ));
        }
    }

    /*
     * Get controller names where the module is supposed to modify the block
     */
    private function getControllerNames($type = false)
    {
        $controllerNames = array();
        if (!$type || $type == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            array_push($controllerNames, 'catalog_product');
        }
        return $controllerNames;
    }

    private function _initBlocks()
    {
        if (!Mage::helper('xtento_productexport')->getModuleEnabled() || !Mage::helper('xtento_productexport')->isModuleProperlyInstalled()) {
            return false;
        }
        return true;
    }
}