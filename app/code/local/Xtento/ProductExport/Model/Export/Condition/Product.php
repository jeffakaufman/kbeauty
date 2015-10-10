<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-07-30T23:42:03+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Condition/Product.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Condition_Product extends Mage_SalesRule_Model_Rule_Condition_Product
{
    public function getValueSelectOptions()
    {
        switch ($this->getAttribute()) {
            case 'type_id':
                return Mage::getModel('catalog/product_type')->getOptions();
        }
        return parent::getValueSelectOptions();
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'type_id':
                return 'select';
        }
        return parent::getInputType();
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'type_id':
                return 'select';
        }
        return parent::getValueElementType();
    }

    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!$attribute->isAllowedForRuleCondition() /* || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)*/) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        // Add custom attributes
        $attributes['qty'] = 'Quantity in stock';
        $attributes['type_id'] = 'Product Type';

        // Remove certain attributes
        foreach ($attributes as $attributeCode => $label) {
            if (preg_match('/^quote_item_/', $attributeCode)) {
                unset($attributes[$attributeCode]);
            }
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        #Zend_Debug::dump($object->getData());
        #Zend_Debug::dump("result: ".$this->validateAttribute($object->getData($this->getAttribute())), "expected: ".$object->getData($this->getAttribute()));
        if ($this->getAttribute() == 'category_ids') {
            // Load category_ids before validation
            $object->getCategoryIds();
        }
        return $this->validateAttribute($object->getData($this->getAttribute()));
    }
}
