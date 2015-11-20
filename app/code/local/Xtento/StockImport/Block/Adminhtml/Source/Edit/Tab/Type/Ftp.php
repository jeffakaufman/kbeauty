<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-11-11T15:51:10+01:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Source/Edit/Tab/Type/Ftp.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Source_Edit_Tab_Type_Ftp
{
    // FTP Configuration
    public function getFields($form, $type = 'FTP')
    {
        $model = Mage::registry('source');
        if ($type == 'FTP') {
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_stockimport')->__('FTP Configuration'),
            ));
        } else {
            // SFTP
            $fieldset = $form->addFieldset('config_fieldset', array(
                'legend' => Mage::helper('xtento_stockimport')->__('SFTP Configuration'),
            ));
            $fieldset->addField('sftp_note', 'note', array(
                'text' => Mage::helper('xtento_stockimport')->__('<strong>Important</strong>: Only SFTPv3 servers are supported. Please make sure the server you\'re trying to connect to is a SFTPv3 server.')
            ));
        }

        $fieldset->addField('hostname', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('IP or Hostname'),
            'name' => 'hostname',
            'note' => Mage::helper('xtento_stockimport')->__(''),
            'required' => true,
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_type', 'select', array(
                'label' => Mage::helper('xtento_stockimport')->__('Server Type'),
                'name' => 'ftp_type',
                'options' => array(
                    Xtento_StockImport_Model_Source_Ftp::TYPE_FTP => 'FTP',
                    Xtento_StockImport_Model_Source_Ftp::TYPE_FTPS => 'FTPS ("FTP SSL")',
                ),
                'note' => Mage::helper('xtento_stockimport')->__('FTPS is only available if PHP has been compiled with OpenSSL support. Only some server versions are supported, support is limited by PHP.')
            ));
        }
        $fieldset->addField('port', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Port'),
            'name' => 'port',
            'note' => Mage::helper('xtento_stockimport')->__('Default Port: %d', ($type == 'FTP') ? 21 : 22),
            'class' => 'validate-number',
            'required' => true,
        ));
        $fieldset->addField('username', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Username'),
            'name' => 'username',
            'note' => Mage::helper('xtento_stockimport')->__(''),
            'required' => true,
        ));
        $fieldset->addField('new_password', 'obscure', array(
            'label' => Mage::helper('xtento_stockimport')->__('Password'),
            'name' => 'new_password',
            'note' => Mage::helper('xtento_stockimport')->__(''),
            'required' => true,
        ));
        $model->setNewPassword(($model->getPassword()) ? '******' : '');
        $fieldset->addField('timeout', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Timeout'),
            'name' => 'timeout',
            'note' => Mage::helper('xtento_stockimport')->__('Timeout in seconds after which the connection to the server fails'),
            'required' => true,
            'class' => 'validate-number'
        ));
        if ($type == 'FTP') {
            $fieldset->addField('ftp_pasv', 'select', array(
                'label' => Mage::helper('xtento_stockimport')->__('Enable Passive Mode'),
                'name' => 'ftp_pasv',
                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note' => Mage::helper('xtento_stockimport')->__('If your server is behind a firewall, or if the extension has problems downloading the import files, please set this to "Yes".')
            ));
        }
        $fieldset->addField('path', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Import Directory'),
            'name' => 'path',
            'note' => Mage::helper('xtento_stockimport')->__('This is the absolute path to the directory on the server where files will be downloaded from. This directory has to exist on the FTP server.'),
            'required' => true,
        ));
        $fieldset->addField('filename_pattern', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Filename Pattern'),
            'name' => 'filename_pattern',
            'note' => Mage::helper('xtento_stockimport')->__('This needs to be a valid regular expression. The regular expression will be used to detect import files. The import will fail if the pattern is invalid. Example: /csv/i for all files with the csv file extension or for all files in the import directory: //'),
            'required' => true,
            'class' => 'validate-regex-pattern',
            'after_element_html' => $this->_getRegexValidatorJs()
        ));
        $fieldset->addField('archive_path', 'text', array(
            'label' => Mage::helper('xtento_stockimport')->__('Archive Directory'),
            'name' => 'archive_path',
            'note' => Mage::helper('xtento_stockimport')->__('If you want to move the imported file(s) to another directory after they have been processed, please enter the path here. This is the absolute path to the archive directory on the FTP server. This directory has to exist on the FTP server. Leave empty if you don\'t want to archive the import files.'),
            'required' => false,
        ));
        $fieldset->addField('delete_imported_files', 'select', array(
            'label' => Mage::helper('xtento_stockimport')->__('Delete imported files'),
            'name' => 'delete_imported_files',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_stockimport')->__('Set this to "Yes" if you want to delete the imported file from the FTP server after it has been processed. You can\'t delete and archive at the same time, so choose either this option or the archive option above.')
        ));
    }

    private function _getRegexValidatorJs()
    {
        $errorMsg = Mage::helper('xtento_stockimport')->__('This is no valid regular expression. It needs to begin and end with slashes: /sample/');
        $js = <<<EOT
<script>
Validation.add('validate-regex-pattern', '{$errorMsg}', function(v) {
     if (v == "") {
        return true;
     }
     return RegExp("^\/(.*)\/","gi").test(v);
});
</script>
EOT;
        return $js;
    }
}