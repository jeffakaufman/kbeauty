<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-09-23T16:40:36+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Source/Edit/Tab/Type/Httpdownload.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Source_Edit_Tab_Type_Httpdownload
{
    // HTTP Configuration
    public function getFields($form)
    {
        $fieldset = $form->addFieldset('config_fieldset', array(
            'legend' => Mage::helper('xtento_stockimport')->__('HTTP Download Configuration'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('http_note', 'note', array(
            'text' => Mage::helper('xtento_stockimport')->__('<b>Instructions</b>: This source is able to download files from a HTTP server. Please supply an URL in the following format: <b></b>http://www.url.com/file.csv</b> - It can be any url with any valid path/filename that exists on the remote webserver. To provide a username/password in the URL, please use: <b>http://username:password@www.url.com/file.csv</b>')
        ));

        $fieldset->addField('custom_function', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('URL'),
            'name' => 'custom_function',
            'required' => true
        ));
    }
}