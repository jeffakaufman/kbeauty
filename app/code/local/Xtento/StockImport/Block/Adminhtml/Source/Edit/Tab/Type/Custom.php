<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-08-12T17:37:45+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Source/Edit/Tab/Type/Custom.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Source_Edit_Tab_Type_Custom
{
    // Custom Type Configuration
    public function getFields($form)
    {
        $fieldset = $form->addFieldset('config_fieldset', array(
            'legend' => Mage::helper('xtento_stockimport')->__('Custom Type Configuration'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('custom_class', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Custom Class Identifier'),
            'name' => 'custom_class',
            'note' => Mage::helper('xtento_stockimport')->__('You can set up an own class in our (or another) module which gets called when importing. The loadFiles() function would be called in your class. If your class was called Xtento_StockImport_Model_Source_Myclass then the identifier to enter here would be xtento_stockimport/source_myclass<br/><br/>The loadFiles() function needs to return an array like this: array(array(\'source_id\' => $this->getSource()->getId(), \'filename\' => $filename, \'data\' => $fileContents))'),
            'required' => true
        ));
    }
}