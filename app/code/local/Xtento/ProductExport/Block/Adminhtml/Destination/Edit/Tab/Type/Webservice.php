<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:06:03+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Edit/Tab/Type/Webservice.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Edit_Tab_Type_Webservice
{
    // Webservice Configuration
    public function getFields($form)
    {
        $fieldset = $form->addFieldset('config_fieldset', array(
            'legend' => Mage::helper('xtento_productexport')->__('Webservice Configuration'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('webservice_note', 'note', array(
            'text' => Mage::helper('xtento_productexport')->__('<b>Instructions</b>: To export data to a webservice, please follow the following steps:<br>1) Go into the <i>app/code/local/Xtento/ProductExport/Model/Destination/</i> directory and rename the file "Webservice.php.sample" to "Webservice.php"<br>2) Enter the function name you want to call in the Webservice.php class in the field below.<br>3) Open the Webservice.php file and add a function that matches the function name you entered. This function will be called by this destination upon exporting then.<br><br><b>Example:</b> If you enter server1 in the function name field below, a method called server1($fileArray) must exist in the Webservice.php file. This way multiple webservices can be added to the Webservice class, and can be called from different export destination, separated by the function name that is called. The function you add then gets called whenever this destination is executed by an export profile.')
        ));

        $fieldset->addField('custom_function', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Custom Function'),
            'name' => 'custom_function',
            'note' => Mage::helper('xtento_productexport')->__('Please make sure the function you enter exists like this in the app/code/local/Xtento/ProductExport/Model/Destination/Webservice.php file:<br>public function <i>yourFunctionName</i>($fileArray) { ... }'),
            'required' => true
        ));
    }
}