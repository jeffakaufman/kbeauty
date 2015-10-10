<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:58:54+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Edit/Tab/Type/Ftp.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Edit_Tab_Type_Ftp
{
    // FTP Configuration
    public function getFields($form, $type = 'FTP')
    {
        $model = Mage::registry('product_export_destination');
        if ($type == 'FTP') {
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_productexport')->__('FTP Configuration'),
            ));
        } else {
            // SFTP
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_productexport')->__('SFTP Configuration'),
            ));
            $fieldset->addField('sftp_note', 'note', array(
                'text' => Mage::helper('xtento_productexport')->__('<strong>Important</strong>: Only SFTPv3 servers are supported. Please make sure the server you\'re trying to connect to is a SFTPv3 server.')
            ));
        }

        $fieldset->addField('hostname', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('IP or Hostname'),
            'name' => 'hostname',
            'note' => Mage::helper('xtento_productexport')->__(''),
            'required' => true,
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_type', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Server Type'),
                'name' => 'ftp_type',
                'options' => array(
                    Xtento_ProductExport_Model_Destination_Ftp::TYPE_FTP => 'FTP',
                    Xtento_ProductExport_Model_Destination_Ftp::TYPE_FTPS => 'FTPS ("FTP SSL")',
                ),
                'note' => Mage::helper('xtento_productexport')->__('FTPS is only available if PHP has been compiled with OpenSSL support. Only some server versions are supported, support is limited by PHP.')
            ));
        }
        $fieldset->addField('port', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Port'),
            'name' => 'port',
            'note' => Mage::helper('xtento_productexport')->__('Default Port: %d', ($type == 'FTP') ? 21 : 22),
            'class' => 'validate-number',
            'required' => true,
        ));
        $fieldset->addField('username', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Username'),
            'name' => 'username',
            'note' => Mage::helper('xtento_productexport')->__(''),
            'required' => true,
        ));
        $fieldset->addField('new_password', 'obscure', array(
            'label' => Mage::helper('xtento_productexport')->__('Password'),
            'name' => 'new_password',
            'note' => Mage::helper('xtento_productexport')->__(''),
            'required' => true,
        ));
        $model->setNewPassword(($model->getPassword()) ? '******' : '');
        $fieldset->addField('timeout', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Timeout'),
            'name' => 'timeout',
            'note' => Mage::helper('xtento_productexport')->__('Timeout in seconds after which the connection to the server fails'),
            'required' => true,
            'class' => 'validate-number'
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_pasv', 'select', array(
                'label' => Mage::helper('xtento_productexport')->__('Enable Passive Mode'),
                'name' => 'ftp_pasv',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_productexport')->__('If your server is behind a firewall, or if the extension has problems uploading the exported files, please set this to "Yes".')
            ));
        }
        $fieldset->addField('path', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Export Directory'),
            'name' => 'path',
            'note' => Mage::helper('xtento_productexport')->__('This is the absolute path to the directory on the server where files will be uploaded to. This directory has to exist on the FTP server.'),
            'required' => true,
        ));
    }
}