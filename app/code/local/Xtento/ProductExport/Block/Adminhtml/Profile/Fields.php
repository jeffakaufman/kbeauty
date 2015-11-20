<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-05-24T16:26:09+02:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Fields.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Fields extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('xtento/productexport/export_fields.phtml');
    }

    public function getFieldJson()
    {
        $export = Mage::getSingleton('xtento_productexport/export_entity_' . Mage::registry('product_export_profile')->getEntity());
        $export->setShowEmptyFields(1);
        $export->setProfile(Mage::registry('product_export_profile'));
        $export->setCollectionFilters(
            array(array('entity_id' => array('in' => explode(",", $this->getTestId()))))
        );
        $returnArray = $export->runExport();
        if (empty($returnArray)) {
            return false;
        }
        return Zend_Json::encode($this->prepareJsonArray($returnArray));
    }

    /*
     * Convert Array into EXTJS TreePanel JSON
     */
    private function prepareJsonArray($array, $parentKey = '')
    {
        static $depth = 0;
        $newArray = array();

        $depth++;
        if ($depth >= '100') {
            return '';
        }

        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $key = Mage::getSingleton('xtento_productexport/output_xml_writer')->handleSpecialParentKeys($key, $parentKey);
                $newArray[] = array('text' => '<strong>' . $key . '</strong>', 'leaf' => false, 'expanded' => true, 'cls' => 'x-tree-noicon', 'children' => $this->prepareJsonArray($val, $key));
            } else {
                if ($val == '') {
                    $val = Mage::helper('xtento_productexport')->__('NULL');
                }
                $newArray[] = array('text' => $key, 'leaf' => false, 'cls' => 'x-tree-noicon', 'children' => array(array('text' => $val, 'leaf' => true, 'cls' => 'x-tree-noicon')));
            }
        }
        return $newArray;
    }

    public function getTestId()
    {
        return urldecode($this->getRequest()->getParam('test_id'));
    }
}