<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-05-29T14:09:28+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Condition/Product/Found.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Condition_Product_Found extends Mage_SalesRule_Model_Rule_Condition_Product_Found
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('xtento_productexport/export_condition_product_found');
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('salesrule')->__("If an item is %s with %s of these conditions true:",
                $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('xtento_productexport/export_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        #$iAttributes = array();
        foreach ($productAttributes as $code => $label) {
            #if (strpos($code, 'quote_item_') === 0) {
            #    $iAttributes[] = array('value' => 'xtento_productexport/export_condition_product|' . $code, 'label' => $label);
            #} else {
            $pAttributes[] = array('value' => 'xtento_productexport/export_condition_product|' . $code, 'label' => $label);
            #}
        }

        #$itemAttributes = array();
        #$customItemAttributes = Mage::getModel('xtento_productexport/export_condition_custom')->getCustomNotMappedAttributes('_item');
        #foreach ($customItemAttributes as $code => $label) {
        #    $itemAttributes[] = array('value' => 'xtento_productexport/export_condition_item|' . $code, 'label' => $label);
        #}

        $conditions = array(
            #array('value' => 'salesrule/rule_condition_product_combine', 'label' => Mage::helper('catalog')->__('Conditions Combination')),
            #array('label' => Mage::helper('catalog')->__('Cart Item Attribute'), 'value' => $iAttributes),
            array('label' => Mage::helper('catalog')->__('Product Attribute'), 'value' => $pAttributes),
            #array('label' => Mage::helper('catalog')->__('Item Attribute'), 'value' => $itemAttributes),
        );
        return $conditions;
    }

    public function validate(Varien_Object $object)
    {
        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();
        #$found = false;

        $found = $all;
        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);
            if (($all && !$validated) || (!$all && $validated)) {
                $found = $validated;
            }
        }
        if (($found && $true) || (!$true && $found)) {
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } // not found and we're making sure it doesn't exist
        elseif (!$found && !$true) {
            return true;
        }
        return false;
    }
}
